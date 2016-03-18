/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

angular
    .module('controller.modal.duplicate', [])
    .controller('duplicate', ['$scope', '$modalInstance', '$http', 'item', 'profiles',
        function ($scope, $modalInstance, $http, item, profiles) {

            /**
             * @type {}
             */
            $scope.setting = item.setting;

            /**
             * @type string
             */
            $scope.newProfile = 'default';

            /**
             * @type []
             */
            $scope.profiles = profiles;

            /**
             * Copies setting to another profile
             *
             * @param newProfile string
             */
            $scope.copy = function (newProfile) {
                requestUrl = Routing.generate('ongr_settings_setting_copy', {
                    name: $scope.setting.name,
                    from: $scope.setting.profile,
                    to: newProfile
                });

                $http({ method: "GET", url: requestUrl  })
                    .success(function (data, status) {
                        $modalInstance.close();
                        window.location.reload();
                    });
            };

            /**
             * closes modal
             */
            $scope.cancel = function () {
                $modalInstance.dismiss('cancel');
            };
        }
    ]);
