'use strict';

var audirvanaApp = angular.module( 'audirvanaApp', [] );

audirvanaApp.controller( 'AudirvanaAppCtrl', function( $scope, $http ) 
{
	$http.get( 'getAlbums.php' ).success( function( data )
	{
		$scope.albums = data;
		$scope.orderProp = 'ZALBUMARTISTSNAMES';
	});

	$scope.appVersion = '1.0';

});

