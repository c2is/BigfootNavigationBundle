<?php

namespace Bigfoot\Bundle\NavigationBundle\Twig\Extension;

use Symfony\Component\Routing\RouterInterface;
use Twig_Extension;

use Bigfoot\Bundle\NavigationBundle\Entity\Menu\Item;
use Bigfoot\Bundle\NavigationBundle\Manager\Menu\Item\UrlManager;

/**
 * LinkExtension
 *
 * @package Bigfoot\Bundle\NavigationBundle\Twig
 */
class LinkExtension extends Twig_Extension
{
    /**
     * @var \Bigfoot\Bundle\NavigationBundle\Manager\Menu\Item\UrlManager
     */
    private $urlManager;

    /**
     * Construct LinkExtension
     *
     * @param UrlManager $urlManager
     */
    public function __construct(UrlManager $urlManager)
    {
        $this->urlManager = $urlManager;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('link_url', array($this, 'getLink')),
        );
    }

    /**
     * @param array $link
     * @param bool $absolute
     *
     * @return string The generated URL for the $link bigfoot link
     */
    public function getLink($link, $absolute = true)
    {
        return $this->urlManager->getLink($link, $absolute);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bigfoot_link';
    }
}
