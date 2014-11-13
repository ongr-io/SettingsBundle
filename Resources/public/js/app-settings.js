/*
 *************************************************************************
 * NFQ eXtremes CONFIDENTIAL
 * [2013] - [2014] NFQ eXtremes UAB
 * All Rights Reserved.
 *************************************************************************
 * NOTICE:
 * All information contained herein is, and remains the property of NFQ eXtremes UAB.
 * Dissemination of this information or reproduction of this material is strictly forbidden
 * unless prior written permission is obtained from NFQ eXtremes UAB.
 *************************************************************************
 */

angular
    .module('fox.settings', [
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
