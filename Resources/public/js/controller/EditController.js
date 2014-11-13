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
    .module('controller.edit', [])
    .controller('edit', ['$scope', '$http', 'DATA', 'settingService',
        function($scope, $http, DATA, settingService) {

            /**
             * Setting service
             *
             * @type {{}}
             */
            $scope.settingService = settingService;

            /**
             * Setting settings data
             */
            $scope.settingService.setSetting(DATA);

            /**
             * Set current setting type index
             */
            angular.forEach($scope.settingService.settingTypes,
                function(value, key) {
                    if (value['type'] == $scope.settingService.setting.type) {
                        $scope.settingService.selectedTypeIndex = key;
                    }
                });
        }]);

