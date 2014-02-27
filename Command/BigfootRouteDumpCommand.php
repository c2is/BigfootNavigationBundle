<?php

namespace Bigfoot\Bundle\NavigationBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Bigfoot\Bundle\CoreBundle\Command\BaseCommand;
use Bigfoot\Bundle\NavigationBundle\Entity\Route;
use Bigfoot\Bundle\NavigationBundle\Entity\Route\Parameter;

/**
 * BigfootRouteDumpCommand
 */
class BigfootRouteDumpCommand extends BaseCommand
{
    /**
     * Configure
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('bigfoot:route:dump')
            ->setDescription('Dump route')
            ->setHelp(
                <<<HELP
The <info>bigfoot:route:dump</info> command dump routes.

<info>php app/console bigfoot:route:dump</info>
HELP
            );
    }

    /**
     * Execute
     *
     * @param InputInterface  $input  input
     * @param OutputInterface $output output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Bigfoot route dump</info>');

        try {
            $entityManager = $this->getEntityManager();
            $router        = $this->getRouter();
            $routes        = $router->getRouteCollection();

            foreach ($routes as $key => $route) {
                $options = $route->getOptions();
                $dbRoute = $entityManager->getRepository('BigfootNavigationBundle:Route')->findOneByName($key);

                if ($dbRoute) {
                    if (isset($options['parameters'])) {
                        foreach ($options['parameters'] as $parameter) {
                            $dbParameter = $entityManager->getRepository('BigfootNavigationBundle:Route\Parameter')->findOneByRouteName($dbRoute, $parameter['name']);

                            if ($dbParameter) {
                                $dbParameter
                                    ->setName($parameter['name'])
                                    ->setType($parameter['type'])
                                    ->setLabelField($parameter['label'])
                                    ->setValueField($parameter['value']);
                            } else {
                                $this->createParameter($route, $parameter);
                            }
                        }
                    }
                } elseif (isset($route->getOptions()['label'])) {
                    $nRoute = new Route();
                    $nRoute->setName($key)
                           ->setLabel($options['label']);

                    $this->getEntityManager()->persist($nRoute);

                    if (isset($options['parameters'])) {
                        foreach ($options['parameters'] as $parameter) {
                            $this->createParameter($route, $parameter);
                        }
                    }
                }
            }

            $output->writeln(' > <info>Flushing</info>');
            $entityManager->flush();
            $output->writeln(' > <comment>OK</comment>');
        } catch (Exception $e) {
            $output->writeln(' > <error>Erreur : ' . $e->getMessage() . '</error>');
        }
    }

    protected function createParameter($route, $parameter)
    {
        $nParameter = new Parameter();
        $nParameter
            ->setName($parameter['name'])
            ->setType($parameter['type'])
            ->setLabelField($parameter['label'])
            ->setValueField($parameter['value'])
            ->setRoute($route);

        $this->getEntityManager()->persist($nParameter);
    }
}
