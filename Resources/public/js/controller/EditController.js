/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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

