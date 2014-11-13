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
    .module('util.asset', [])
    .service('asset', ['$location', function(location) {

        /**
         * Returns a link for js asset
         *
         * @param asset string
         */
        this.getLink = function(asset) {
            host = location.$$host;
            protocol = location.$$protocol;

            return protocol + '://' + host +'/bundles/foxadmin/js/' + asset;
        }
    }]);
