<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10/9/18
 * Time: 11:50 AM
 */

namespace Fynduck\VideoUrlParser;

use Illuminate\Support\Str;

class VideoUrlParse
{
    protected $url;

    protected $video_src;

    public static function url(string $url): VideoUrlParse
    {
        return new static($url);
    }

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * Validation url
     */
    public function isValidURL(): bool
    {
        $result = preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $this->url);

        if (!$result) {
            return false;
        }

        switch ($this->url) {
            case Str::contains($this->url, 'rutube.ru'):
                return $this->isCorrectRutube();
            case Str::contains($this->url, 'youtube.com'):
            case Str::contains($this->url, 'youtu.be'):
                return $this->isCorrectYoutube();
            case Str::contains($this->url, 'vimeo.com'):
                return $this->isCorrectVimeo();
        }

        return false;
    }

    /**
     * validation video url: Youtube & Rutube & Vimeo
     * @return bool
     */
    protected function checkIsUrlCorrect(): bool
    {
        switch ($this->url) {
            case Str::contains($this->url, 'rutube.ru'):
                return $this->isCorrectRutube();
            case Str::contains($this->url, 'youtube.com'):
            case Str::contains($this->url, 'youtu.be'):
                return $this->isCorrectYoutube();
            case Str::contains($this->url, 'vimeo.com'):
                return $this->isCorrectVimeo();
        }

        return false;
    }

    /**
     * Check if is rutube link
     * @return bool
     */
    protected function isCorrectRutube(): bool
    {
        $urlParams = array_filter(explode('/', $this->url));

        if (isset($urlParams[3]) && $urlParams[3] == 'video' && (isset($urlParams[4]) && $urlParams[4]))
            return 'rutube';
        elseif (isset($urlParams[3]) && $urlParams[3] == 'play' && (isset($urlParams[4]) && $urlParams[4] == 'embed'))
            return 'rutube';

        return false;
    }

    /**
     * Check if youtube link
     * @return bool
     */
    protected function isCorrectYoutube(): bool
    {
        $urlParams = array_filter(explode('/', $this->url));
        if (isset($urlParams[3])) {
            $checkWatch = explode('?', $urlParams[3]);
            if ($urlParams[3] == 'embed' && isset($urlParams[4]) && $urlParams[4])
                return 'youtube';
            elseif ($urlParams[2] == 'youtu.be' && isset($urlParams[3]) && $urlParams[3])
                return 'youtube';
            elseif ($checkWatch[0] == 'watch' && isset($checkWatch[1]) && (explode('=', $checkWatch[1])[0] == 'v'))
                return 'youtube';
        }

        return false;
    }

    /**
     * Check  if is vimeo link
     * @return bool
     */
    protected function isCorrectVimeo(): bool
    {
        $urlParams = array_filter(explode('/', $this->url));

        if (isset($urlParams[2]) && $urlParams[2] == 'vimeo.com' && isset($urlParams[3]) && $urlParams[3])
            return 'vimeo';
        elseif (isset($urlParams[3]) && $urlParams[3] == 'video' && (isset($urlParams[4]) && $urlParams[4]))
            return 'vimeo';

        return false;
    }

    /**
     * src for embed
     * @param bool $domain
     * @return string
     */
    public function returnSrcForEmbed($domain = false)
    {
        if (!$domain) {
            $correctUrl = $this->checkIsUrlCorrect();

            if ($correctUrl)
                return $this->returnSrcForEmbed($correctUrl);
        }

        switch ($domain) {
            case 'youtube':
                $this->youtubeSrc();
                break;
            case 'rutube':
                $this->rutubeSrc();
                break;
            case 'vimeo':
                $this->vimeoSrc();
                break;
            default:
                $this->video_src = $this->url;
        }

        return $this->video_src;
    }

    /**
     * Get youtube embed src
     * @return VideoUrlParse
     */
    protected function youtubeSrc(): VideoUrlParse
    {
        $getParams = [];
        parse_str(parse_url($this->url, PHP_URL_QUERY), $getParams);
        $checkEmbed = array_filter(explode('/', $this->url));
        if ($getParams && isset($getParams['v']) && $getParams['v']) {
            $this->video_src = 'https://www.youtube.com/embed/' . $getParams['v'];
        } elseif (isset($checkEmbed[3]) && $checkEmbed[3] == 'embed' && isset($checkEmbed[4]) && $checkEmbed[4]) {
            $this->video_src = $this->url;
        }

        return $this;
    }

    /**
     * Get youtube embed src
     * @return VideoUrlParse
     */
    protected function rutubeSrc(): VideoUrlParse
    {
        $checkEmbed = array_filter(explode('/', $this->url));

        if (isset($checkEmbed[3]) && $checkEmbed[3] == 'video' && (isset($checkEmbed[4]) && $checkEmbed[4])) {
            $this->video_src = 'https://rutube.ru/play/embed/' . $checkEmbed[4];
        } elseif (isset($checkEmbed[4]) && $checkEmbed[4] == 'embed' && isset($checkEmbed[5]) && $checkEmbed[5]) {
            $this->video_src = $this->url;
        }

        return $this;
    }

    /**
     * Get vimeo embed src
     * @return VideoUrlParse
     */
    protected function vimeoSrc(): VideoUrlParse
    {
        $checkEmbed = array_filter(explode('/', $this->url));

        if (isset($checkEmbed[2]) && $checkEmbed[2] == 'vimeo.com' && (isset($checkEmbed[3]) && $checkEmbed[3])) {
            $this->video_src = 'https://player.vimeo.com/video/' . $checkEmbed[3];
        } elseif (isset($checkEmbed[3]) && $checkEmbed[3] == 'video' && isset($checkEmbed[4]) && $checkEmbed[4]) {
            $this->video_src = $this->url;
        }

        return $this;
    }
}
