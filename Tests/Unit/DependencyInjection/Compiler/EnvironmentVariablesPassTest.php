<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\AdminBundle\Tests\Functional\DependencyInjection\Compiler;

use ONGR\AdminBundle\DependencyInjection\Compiler\EnvironmentVariablesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This class contains function tests for EnvironmentVariablesPass.
 */
class EnvironmentVariablesPassTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Data provider for testProcess.
     *
     * @return array
     */
    public function dataProcess()
    {
        // Case #0 simple common test case.
        $variables = ['ongr__ongr_utils__environment_variables_pass_test_param' => 'test'];
        $expectedParameter = ['ongr_utils.environment_variables_pass_test_param' => 'test'];
        $out[] = [$variables, $expectedParameter];

        // Case #1 FOXX shouldn't be matched.
        $variables = ['FOXX__ongr_utils__environment_variables_pass_test_param' => 'test'];
        $expectedParameter = [];
        $out[] = [$variables, $expectedParameter];

        // Case #2 One underscore shouldn't be sufficient.
        $variables = ['ongr_ongr_utils__environment_variables_pass_test_param' => 'test'];
        $expectedParameter = [];
        $out[] = [$variables, $expectedParameter];

        // Case #3 All double underscores after prefix should be replace into dots, letters should be underscore.
        $variables = ['ongr__H__E__L__L__O' => 'test'];
        $expectedParameter = ['h.e.l.l.o' => 'test'];
        $out[] = [$variables, $expectedParameter];

        return $out;
    }

    /**
     * Test if strings are parsed correctly.
     *
     * @param array $variables
     * @param array $expectedParameter
     *
     * @dataProvider dataProcess()
     */
    public function testProcess(array $variables, array $expectedParameter)
    {
        $_SERVER = array_merge($_SERVER, $variables);
        $container = new ContainerBuilder();

        $pass = new EnvironmentVariablesPass();
        $pass->process($container);

        $this->assertEquals($expectedParameter, $container->getParameterBag()->all());

        $_SERVER = array_diff_key($_SERVER, $variables);
    }
}
