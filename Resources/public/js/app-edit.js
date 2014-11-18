/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

angular
    .module('ongr.edit', [
        'ui.bootstrap',
        'controller.edit',
        'directive.yaml',
        'service.setting'
    ])
    .constant('DATA', setting)
    .run(['$templateCache', function($templateCache) {
        $templateCache.put('yaml.textarea', '<textarea id="yaml" class="form-control" cols="30" rows="12"></textarea>');
    }]);
