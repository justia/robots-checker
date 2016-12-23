<?php

namespace NickMoline\Robots;

use League\Uri\Schemes\Http as HttpUri;
use Curl\Curl;

class Robots
{
    protected $url;
    protected $urlInfo;
    protected $curl;
    protected $allowed = true;
    protected $reason;
    protected $label = null;
    protected $fetched = false;

    public function __construct($url = null)
    {
        if ($url) {
            $this->setURL($url);
        }
    }

    public static function createFromExisting(Robots $existing, Robots $robots = null)
    {
        if (!$robots) {
            $robots = new Robots();
        }

        $robots->setURL($existing->getURL())
               ->setCurl($existing->checkerCurl())
               ->setAllowed($existing->isAllowed())
               ->setReason($existing->getReason())
               ->setLabel($existing->getLabel());

        return $robots;
    }

    public function isAllowed()
    {
        return $this->allowed;
    }

    public function setAllowed($allowed = true, $reason = null, $label = null)
    {
        $this->allowed = $allowed;
        if ($reason) {
            $this->setReason($reason);
        }
        if ($this->label) {
            $this->setLabel($label);
        }

        return $this;
    }

    public function setDenied($reason = null, $label = null)
    {
        return $this->setAllowed(false, $reason, $label);
    }

    public function getLabel()
    {
        if ($this->label) {
            return $this->label;
        }
        return ($this->isAllowed())?'Allowed':'Denied';
    }

    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    public function getReason()
    {
        return $this->reason;
    }

    public function setReason($reason)
    {
        $this->reason = $reason;
        return $this;
    }

    public function getURL()
    {
        return $this->url;
    }

    public function setURL($url)
    {
        $this->url = $url;
        $this->urlInfo = null;

        return $this;
    }

    public function urlInfo()
    {
        if ($this->urlInfo) {
            return $this->urlInfo;
        }

        $this->urlInfo = HttpUri::createFromString($this->url);
        return $this->urlInfo;
    }

    public function getHomepageUrl()
    {
        $uri = $this->urlInfo();

        return HttpUri::createFromString($this->url)->withPath("/");
    }

    public function getRelativeUrl()
    {
        return $this->urlInfo()->getPath();
    }

    public function checkerCurl($userAgent = "Robots.TXT Checker v1.0")
    {
        if (!isset($this->curl)) {
            $this->curl = new Curl();
            $this->curl->setUserAgent($userAgent);
        }

        return $this->curl;
    }

    public function setCurl(Curl $curl)
    {
        $this->curl = $curl;
        return $this;
    }

    public function setFetched($fetched = true)
    {
        $this->fetched = $fetched;
        return $this;
    }

    public function isFetched()
    {
        return $this->fetched;
    }
}
