<?php
/**
 * Created by PhpStorm.
 * User: cedric
 * Date: 22/02/18
 * Time: 13:40
 */

namespace AppBundle\Command;

use AppBundle\Security\CredentialsCacheService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 */
class CacheClearCommand extends Command
{
    /** @var CredentialsCacheService $cache */
    private $cache;

    public function __construct (
        $name = null,
        CredentialsCacheService $cache
    ) {
        parent::__construct($name);
        $this->cache = $cache;
    }

    protected function configure ()
    {
        $this
            ->setName('app:cache:clear')
            ->setDescription("Clears the app cache");
    }

    protected function execute (InputInterface $input, OutputInterface $output)
    {
        $this->cache->clear();
    }
}
