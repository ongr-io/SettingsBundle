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
    .module('controller.modal.addSetting', [])
    .controller('addSetting', ['$scope', '$modalInstance', '$http', 'domains', 'settingService',
        'settingsList', 'currentDomain',
        function($scope, $modalInstance, $http, domains, settingService,
                 settingsList, currentDomain) {

            /**
             * @type {[]}
             */
            $scope.domains = domains;

            /**
             * Setting service
             *
             * @type {{}}
             */
            $scope.settingService = settingService;

            /**
             * Sets form values to default
             */
            $scope.settingService.clearValues();

            /**
             * Sets list that will be updated
             */
            $scope.settingService.setSettingsList(settingsList);

            /**
             * Set current domain
             */
            $scope.settingService.setCurrentDomain(currentDomain);

            /**
             * Closes modal and reload page if settings were added
             */
            $scope.cancel = function() {
                $modalInstance.close();
            };
        }
    ]);
