/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

angular
    .module('controller.modal.addSetting', [])
    .controller('addSetting', ['$scope', '$modalInstance', '$http', 'profiles', 'settingService',
        'settingsList', 'currentProfile',
        function($scope, $modalInstance, $http, profiles, settingService,
                 settingsList, currentProfile) {

            /**
             * @type {[]}
             */
            $scope.profiles = profiles;

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
             * Set current profile
             */
            $scope.settingService.setCurrentProfile(currentProfile);

            /**
             * Closes modal and reload page if settings were added
             */
            $scope.cancel = function() {
                $modalInstance.close();
            };
        }
    ]);
