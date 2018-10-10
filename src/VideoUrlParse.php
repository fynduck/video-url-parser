<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 10/9/18
 * Time: 11:50 AM
 */

namespace Fynduck\VideoUrlParser;


class VideoUrlParse
{

    /**
     * validation url
     * @param string $url
     * @return boolean
     */
    public function isValidURL($url)
    {
        $result = preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
        if ($result)
            $result = $this->checkIsUrlCorrect($url);

        return $result;
    }

    /**
     * validation video url: Youtube & Rutube & Vimeo
     * @param string $url
     * @return boolean
     */
    public function checkIsUrlCorrect($url)
    {
        switch ($url) {
            case str_contains($url, 'rutube.ru'):
                $result = $this->isCorrectRutube($url);
                break;
            case str_contains($url, 'youtube.com'):
            case str_contains($url, 'youtu.be'):
                $result = $this->isCorrectYoutube($url);
                break;
            case str_contains($url, 'vimeo.com'):
                $result = $this->isCorrectVimeo($url);
                break;
            default:
                $result = false;
        }

        return $result;
    }

    /**
     * Check if is rutube link
     * @param $url
     * @return bool
     */
    protected function isCorrectRutube($url)
    {
        $urlParams = array_filter(explode('/', $url));

        if (isset($urlParams[3]) && $urlParams[3] == 'video' && (isset($urlParams[4]) && $urlParams[4]))
            return 'rutube';
        elseif (isset($urlParams[3]) && $urlParams[3] == 'play' && (isset($urlParams[4]) && $urlParams[4] == 'embed'))
            return 'rutube';

        return false;
    }

    /**
     * Check if youtube link
     * @param $url
     * @return bool
     */
    protected function isCorrectYoutube($url)
    {
        $urlParams = array_filter(explode('/', $url));
        if (isset($urlParams[3])) {
            $checkWatch = explode('?', $urlParams[3]);
            if ($urlParams[3] == 'embed' && isset($urlParams[4]) && $urlParams[4])
                return 'youtube';
            elseif ($checkWatch[0] == 'watch' && isset($checkWatch[1]) && (explode('=', $checkWatch[1])[0] == 'v'))
                return 'youtube';
        }

        return false;
    }

    /**
     * Check  if is vimeo link
     * @param $url
     * @return bool
     */
    protected function isCorrectVimeo($url)
    {
        $urlParams = array_filter(explode('/', $url));

        if (isset($urlParams[2]) && $urlParams[2] == 'vimeo.com' && isset($urlParams[3]) && $urlParams[3])
            return 'vimeo';
        elseif (isset($urlParams[3]) && $urlParams[3] == 'video' && (isset($urlParams[4]) && $urlParams[4]))
            return 'vimeo';

        return false;
    }

    /**
     * src for embed
     * @param string $url
     * @param bool $domain
     * @return string
     */
    public function returnSrcForEmbed($url, $domain = false)
    {
        if (!$domain) {
            $correctUrl = $this->checkIsUrlCorrect($url);

            if ($correctUrl)
                return $this->returnSrcForEmbed($url, $correctUrl);
        }

        switch ($domain) {
            case 'youtube':
                $src = $this->youtubeSrc($url);
                break;
            case 'rutube':
                $src = $this->rutubeSrc($url);
                break;
            case 'vimeo':
                $src = $this->vimeoSrc($url);
                break;
            default:
                $src = $url;
        }

        return $src;
    }

    /**
     * Get youtube embed src
     * @param $url
     * @return string
     */
    protected function youtubeSrc($url)
    {
        $getParams = [];
        parse_str(parse_url($url, PHP_URL_QUERY), $getParams);
        $checkEmbed = array_filter(explode('/', $url));

        if ($getParams && isset($getParams['v']) && $getParams['v'])
            return 'https://www.youtube.com/embed/' . $getParams['v'];
        elseif (isset($checkEmbed[3]) && $checkEmbed[3] == 'embed' && isset($checkEmbed[4]) && $checkEmbed[4])
            return $url;

    }

    /**
     * Get youtube embed src
     * @param $url
     * @return string
     */
    protected function rutubeSrc($url)
    {
        $checkEmbed = array_filter(explode('/', $url));

        if (isset($checkEmbed[3]) && $checkEmbed[3] == 'video' && (isset($checkEmbed[4]) && $checkEmbed[4]))
            return 'https://rutube.ru/play/embed/' . $checkEmbed[4];
        elseif (isset($checkEmbed[4]) && $checkEmbed[4] == 'embed' && isset($checkEmbed[5]) && $checkEmbed[5])
            return $url;

    }

    /**
     * Get vimeo embed src
     * @param $url
     * @return string
     */
    protected function vimeoSrc($url)
    {
        $checkEmbed = array_filter(explode('/', $url));

        if (isset($checkEmbed[2]) && $checkEmbed[2] == 'vimeo.com' && (isset($checkEmbed[3]) && $checkEmbed[3]))
            return 'https://player.vimeo.com/video/' . $checkEmbed[3];
        elseif (isset($checkEmbed[3]) && $checkEmbed[3] == 'video' && isset($checkEmbed[4]) && $checkEmbed[4])
            return $url;

    }

}
