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
    .module('filter.objLimitTo', [])
    .filter('objLimitTo', [function () {
        return function(object, limit) {
            if(Object.keys(object).length < 1){
                return [];
            }

            var out = new Object,
                count = 0;

            angular.forEach(object, function(value, key) {
                if(count >= limit){
                    return false;
                }
                this[key] = value;
                count++;
            }, out);

            return out;
        };
    }]);
