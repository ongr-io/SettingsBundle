/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

angular
    .module('controller.settings', [])
    .controller('settings', ['$scope', '$http', 'DATA', 'profiles', '$modal', 'asset', 'settingsList',
        function($scope, $http, DATA, profiles, $modal, $asset, settingsList) {

            /**
             * @type {}
             */
            settingsList.list = DATA;

            /**
             * @type {}
             */
            $scope.settings = settingsList.list;

            /**
             * @type {}
             */
            $scope.profiles = profiles;

            /**
             * Calls a duplicate modal
             *
             * @param settingId string
             */
            $scope.duplicate = function(settingId) {
                var modalInstance = $modal.open({
                    templateUrl: $asset.getLink('template/duplicateModal.html'),
                    controller: 'duplicate',
                    resolve: {
                        item: function() {
                            return {
                                setting: $scope.settings[settingId]
                            };
                        },
                        profiles: function() {
                            var profileNames = [];

                            angular.forEach($scope.profiles, function(value, key) {
                                this.push(value.value);
                            }, profileNames);

                            return profileNames;
                        }
                    }
                });
            };

            /**
             * Calls new setting modal
             */
            $scope.addSetting = function() {
                var modalInstance = $modal.open({
                    templateUrl: $asset.getLink('template/addSettingModal.html'),
                    controller: 'addSetting',
                    resolve: {
                        profiles: function() {
                            var profileNames = [];

                            angular.forEach($scope.profiles, function(value, key) {
                                this.push(value.value);
                            }, profileNames);

                            return profileNames;
                        }
                    }
                });
            };

            /**
             * Removes setting through api
             *
             * @param $index string
             * @param setting {{}}
             */
            $scope.remove = function($index, setting) {
                $http({
                    method: "DELETE",
                    url: Routing.generate(
                        'ongr_admin_setting_remove',
                        {
                            name: setting.name,
                            profile: setting.profile,
                        }
                    )
                }).
                    success(function(data, status) {
                        $scope.settings.splice($index, 1);
                    });
            };

            /**
             * Saves bool status
             *
             * @param $event Event
             * @param setting {{}}
             */
            $scope.saveStatus = function($event, setting) {
                var status = $event.target.innerHTML;
                var link = Routing.generate(
                    'ongr_admin_setting_ng_edit',
                    {
                        name: setting.name,
                        profile: setting.profile,
                    }
                );

                if (status == undefined) {
                    return;
                }

                status = status == "On"; //true or false
                $http({method: "POST", url: link, data: {setting: {data: {"value": status}}}});
            };

            /**
             * Generates link for editing
             *
             * @param setting
             *
             * @returns string
             */
            $scope.editLink = function(setting) {
                return Routing.generate(
                    'ongr_admin_setting_edit',
                    {
                        name: setting.name,
                        profile: setting.profile
                    }
                );
            }
        }
    ]);
