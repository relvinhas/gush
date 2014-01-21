<?php

namespace Gush\Helper;

use Symfony\Component\Console\Helper\Helper;

class GitHubHelper extends Helper
{
    protected $enum = array(
        'issue' => array(
            'filter' => array(
                'assigned',
                'created',
                'mentioned',
                'subscribed',
                'all',
            ),
            'state' => array(
                'open',
                'closed',
            ),
            'sort' => array(
                'created',
                'updated',
            ),
            'direction' => array('asc', 'desc'),
            'type' => array('pr', 'issue'),
        ),
    );

    public function getName()
    {
        return 'github';
    }

    public function validateEnum($domain, $type, $value)
    {
        if (!isset($this->enum[$domain])) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown enum domain "%s"', $domain
            ));
        }

        if (!isset($this->enum[$domain][$type])) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown enum type "%s" in domain "%s',
                $domain, $type
            ));
        }

        if (!in_array($value, $this->enum[$domain][$type])) {
            throw new \InvalidArgumentException(sprintf(
                'Unknown value "%s" for "%s"', $value, $type
            ));
        };
    }
}
