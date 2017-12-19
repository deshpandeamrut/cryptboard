var app = angular.module('selfHelpApp', ['ngRoute', 'ngAria','ngStorage','googlechart']);
app.config(['$routeProvider',
    function ($routeProvider) {
        $routeProvider
        .when('/smartDashboard', {
            templateUrl: 'views/smartDashboard.html',
            controller: 'smartDashboardController'
    
        })
        .when('/graphs', {
            templateUrl: 'views/graphs.html',
            controller: 'graphsController'
        })
        .when('/myBalance', {
            templateUrl: 'views/myBalance.html',
            controller: 'myBalanceController'
        }).
        otherwise({
            redirectTo: '/smartDashboard'
        });
    }]);





$(document).ready(function () {
    console.log("With <3 from AD007");
    // $(".notification-wrapper").hide();
    // console.log('as', $(".notification-wrapper"));

    // Initialize collapse button
  //   $(".button-collapse").sideNav();
  // // Initialize collapsible (uncomment the line below if you use the dropdown variation)
  // $('.collapsible').collapsible();

  $('.button-collapse').sideNav({
      menuWidth: 300, // Default is 240
      edge: 'left', // Choose the horizontal origin
      closeOnClick: true, // Closes side-nav on <a> clicks, useful for Angular/Meteor
      draggable: true // Choose whether you can drag to open on touch screens
  }
  );

   $(document).ready(function(){
    $('.collapsible').collapsible();
    
  });
});

app.service('dataService', function ($sessionStorage) {
    this.currentUserData ;
    var self = this;
    this.getDashboardData = function(){
        return $.ajax({url: './getCurrentPrice.php?',
            success: function (data) {
                return data;
            }
        })
    },
     this.getPriceData = function(){
        return $.ajax({url: './callExchanges.php?',
            success: function (data) {
                return data;
            }
        })
    },
    this.getMyBalance = function(name,pin){
        return $.ajax({url: './balance_new.php?name='+name+'&pin='+pin,
            success: function (data) {
                return data;
            }
        })
    },
    this.getGlobalPrice = function(){
        var priceIndex ="https://blockchain.info/ticker"; 
        return $.ajax({url: priceIndex,
            success: function (data) {
                // console.log(data);
                return data;
            }
        })
    },
    this.getLatestFeed = function(){
        return $.ajax({url: './latestBitcoinFeed.php',
            success: function (data) {
                return data;
            }
        })
    };
    // 

}); 
function doLog(text) {
    var nVer = navigator.appVersion;
    var nAgt = navigator.userAgent;
    var browserName = navigator.appName;
    var fullVersion = '' + parseFloat(navigator.appVersion);
    var majorVersion = parseInt(navigator.appVersion, 10);
    var nameOffset, verOffset, ix;

// In Opera, the true version is after "Opera" or after "Version"
if ((verOffset = nAgt.indexOf("Opera")) != -1) {
    browserName = "Opera";
    fullVersion = nAgt.substring(verOffset + 6);
    if ((verOffset = nAgt.indexOf("Version")) != -1)
        fullVersion = nAgt.substring(verOffset + 8);
}
// In MSIE, the true version is after "MSIE" in userAgent
else if ((verOffset = nAgt.indexOf("MSIE")) != -1) {
    browserName = "Microsoft Internet Explorer";
    fullVersion = nAgt.substring(verOffset + 5);
}
// In Chrome, the true version is after "Chrome" 
else if ((verOffset = nAgt.indexOf("Chrome")) != -1) {
    browserName = "Chrome";
    fullVersion = nAgt.substring(verOffset + 7);
}
// In Safari, the true version is after "Safari" or after "Version" 
else if ((verOffset = nAgt.indexOf("Safari")) != -1) {
    browserName = "Safari";
    fullVersion = nAgt.substring(verOffset + 7);
    if ((verOffset = nAgt.indexOf("Version")) != -1)
        fullVersion = nAgt.substring(verOffset + 8);
}
// In Firefox, the true version is after "Firefox" 
else if ((verOffset = nAgt.indexOf("Firefox")) != -1) {
    browserName = "Firefox";
    fullVersion = nAgt.substring(verOffset + 8);
}
// In most other browsers, "name/version" is at the end of userAgent 
else if ((nameOffset = nAgt.lastIndexOf(' ') + 1) <
    (verOffset = nAgt.lastIndexOf('/')))
{
    browserName = nAgt.substring(nameOffset, verOffset);
    fullVersion = nAgt.substring(verOffset + 1);
    if (browserName.toLowerCase() == browserName.toUpperCase()) {
        browserName = navigator.appName;
    }
}
// trim the fullVersion string at semicolon/space if present
if ((ix = fullVersion.indexOf(";")) != -1)
fullVersion = fullVersion.substring(0, ix);
if ((ix = fullVersion.indexOf(" ")) != -1)
    fullVersion = fullVersion.substring(0, ix);

majorVersion = parseInt('' + fullVersion, 10);
if (isNaN(majorVersion)) {
    fullVersion = '' + parseFloat(navigator.appVersion);
    majorVersion = parseInt(navigator.appVersion, 10);
}


var OSName = "Unknown OS";
if (navigator.appVersion.indexOf("Win") != -1)
    OSName = "Windows";
if (navigator.appVersion.indexOf("Mac") != -1)
    OSName = "MacOS";
if (navigator.appVersion.indexOf("X11") != -1)
    OSName = "UNIX";
if (navigator.appVersion.indexOf("Linux") != -1)
    OSName = "Linux";


userData = {
    'browswername': browserName,
    'version': fullVersion,
    'appName': navigator.appName,
    'userAgent': navigator.userAgent,
    'OS': OSName,
    'userText': text,
    'timestamp': Date()
};
// console.log(userData);
$.post("./doLog.php", {userData: userData}).then(function (data) {
    // console.log(data);
})
}