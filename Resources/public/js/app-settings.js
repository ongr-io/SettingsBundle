/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

angular
    .module('ongr.settings', [
        'ui.bootstrap',
        'controller.settings',
        'controller.modal.duplicate',
        'controller.modal.addSetting',
        'filter.objLimitTo',
        'directive.inline',
        'directive.reallyClick',
        'directive.yaml',
        'directive.collapse',
        'service.setting',
        'util.asset'
    ])
    .factory("settingsList", function(){
        return {list: {data: null } }
    })
    .constant('DATA', setting)
    .constant('currentDomain', currentDomain)
    .constant('domains', domains)
    .run(['$templateCache', function($templateCache) {
        $templateCache.put('yaml.textarea', '<textarea id="yaml" class="form-control" cols="30" rows="12"></textarea>');
    }]);
