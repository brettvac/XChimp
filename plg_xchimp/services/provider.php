<?php
/*****************************************************************
 * @package XChimp
 * @version 2.0
 * @author ThemeXpert
 * @copyright Copyright (C) 2010 - 2011 ThemeXpert. Updates 2025 by Brett.
 * @license http://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 only
 *****************************************************************/

\defined('_JEXEC') or die; //No direct access
 
use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Themexpert\Plugin\User\Xchimp\Extension\Xchimp;

    return new class() implements ServiceProviderInterface
    {
        public function register(Container $container)
        {
            $container->set(
                PluginInterface::class,
                function (Container $container) {
    
                    $config = (array) PluginHelper::getPlugin('user', 'xchimp');
                    $subject = $container->get(DispatcherInterface::class);
                    $app = Factory::getApplication();
                    
                    $plugin = new Xchimp($subject, $config);
                    $plugin->setApplication($app);
    
                    return $plugin;
                }
            );
        }
    };