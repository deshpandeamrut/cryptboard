app.controller('globalCtrl',function($scope,$timeout,dataService){
    $scope.loadData = function(){
        console.log("called");
        dataService.getGlobalPrice().
        then(function(globalPriceData){
            // console.log(globalPriceData);
            $scope.globalPrice = globalPriceData;
            $scope.globalPrice.INR.last = Math.round($scope.globalPrice.INR.last);
            $scope.globalPrice.USD.last = Math.round($scope.globalPrice.USD.last);
            console.log($scope.globalPrice.USD);
            $scope.$apply();
        })
        dataService.getPriceData().
        then(function(priceData){
            $scope.priceData = JSON.parse(priceData);
            angular.forEach($scope.priceData,function(entry){

                if(entry.name=="zebpay"){
                    console.log(entry);    
                    $scope.priceData.zebpay={
                        buy:entry.buy,
                        sell:entry.sell
                    } 
                }else if(entry.name="koinex"){
                    $scope.priceData.koinex={
                        btc:entry.btc.buy,
                        bch:entry.bch.buy,
                        eth:entry.eth.buy,
                        ltc:entry.ltc.buy,
                        xrp:entry.xrp.buy,
                    }
                }
            })
            console.log($scope.priceData);
        })
        
    }
    $scope.loadData();//call  first time
    $timeout(function () {
        console.log("hello");
        $scope.loadData();
    }, 5000);
})
app.controller('smartDashboardController',function($scope,$rootScope,dataService,googleChartApiPromise,$timeout){
    doLog("Visited Dashboard");

    $scope.chartToolName = 'home';
    $scope.chartType = "hourly";
    $scope.myChartObject = {};
    $scope.myChartObject.type = "LineChart";
    $scope.myChartObject.data = {}
    $scope.isChart = false;
    googleChartApiPromise.then(load_chart_data);
    $scope.loadData = function(){
        dataService.getDashboardData().
        then(function(dashboardData){
            $scope.dashboardData = JSON.parse(dashboardData);
            // console.log($scope.dashboardData);
            $scope.$apply();
        })
        /*dataService.getLatestFeed().
        then(function(feedData){
            $scope.feedData = JSON.parse(feedData).articles;
            $scope.feed = [];
            for(var i=0;i<5;i++){
                $scope.feed.push($scope.feedData[i]);  
            }
            // console.log($scope.feed);
            $scope.$apply();
        })*/
        /*
        * Call GlobalPriceIndex
        */


    }
    $scope.loadData();
    $timeout(function () {
        $scope.loadData();
    }, 10000);


    function load_chart_data() {
                        $.ajax({url: './getGraphData.php', // provide correct url
                            type: 'GET',
                            data: {toolType: $scope.chartToolName, chartType: $scope.chartType}
                        }).
                        then(function (chart_values) {
                            chart_values = JSON.parse(chart_values);
                            
                            if (chart_values instanceof  Array) {

                                var data = new google.visualization.DataTable();

                                if ($scope.chartType == "daily") {
                                 var haxisTitle = "Day of the Month";
                                 data.addColumn('date', 'day');
                                 data.addColumn('number', 'Count');

                                 $.each(chart_values, function (i, entry)
                                 {
                                    if (i > 0) {
                                        var day = entry[0];
                                        var date = new Date(day);
                                        var count = entry[1];
                                        data.addRows([[date, count]]);
                                    }

                                });
                             } else {
                                 var haxisTitle = "Time of Day";
                                 data.addColumn('timeofday', 'Time Of Day');
                                 data.addColumn('number', 'Zebpay Price');
                                 data.addColumn('number', 'Koinex Price');

                                 $.each(chart_values, function (i, entry)
                                 {
                                    if (i > 0) {
                                       var hour = Number(entry[0].split(":")[0]);
                                       var min = Number(entry[0].split(":")[1])
                                       var count = entry[1];
                                       var zebpayPrice = Number(count.split(",")[0]);
                                       var koinexPrice = Number(count.split(",")[1]);
                                       data.addRows([[[hour, min, 0], zebpayPrice,koinexPrice]]);
                                   }

                               });
                             }
                            // console.log(data);
                            $scope.isChart = true;
                            $scope.myChartObject.data = data;
                            var options = {
                                title: 'Bitcoin Trend at Zebpay',
                                vAxis: {title: 'Bitcoin Zebpay Price', titleTextStyle: {italic: false}},
                                hAxis: {title: haxisTitle, titleTextStyle: {italic: false}},
                                height:"100%",
                                width:"90%"
                            };
                            $scope.myChartObject.options = options;
                            $scope.$apply();
                        } else {
                            $scope.myChartObject.data = {};
                            $scope.$apply();
                            return;

                        }

                    }
                    );
                    }

                });


app.controller('graphsController',function($scope,dataService,$rootScope,googleChartApiPromise){
    doLog("Visited Graphs");
    googleChartApiPromise.then(load_chart_data);
    $scope.toolList = {};
    $scope.coinList = ['BTC','XRP','ETH','BCHABC','BSV'];
    $scope.coinType = 'BTC';
    $scope.chartToolName = 'home';
    $scope.chartType = "hourly";
    $scope.myChartObject = {};
    $scope.myChartObject.type = "LineChart";
    $scope.myChartObject.data = {}
    $scope.isChartLoaded = false;
    $scope.isChartDataAvailable = true;
    $scope.drawChart = function() {
        console.log($scope.coinType);
        load_chart_data();
    }
    $scope.changeGraphType= function(type){
        load_chart_data();
    }   
    $scope.renderChartType = function(type){
        $scope.myChartObject.type = type;
        // console.log(type);
        // $scope.$apply();
    }
    
    function load_chart_data() {
                        $.ajax({url: './getGraphData.php', // provide correct url
                            type: 'GET',
                            data: {coinType: $scope.coinType.toLowerCase()}
                        }).
                        then(function (chart_values) {
                            chart_values = JSON.parse(chart_values);
                            // console.log(chart_values);
                            var title = 'Bitcoin Trend | Zebpay vs Koinex vs Global';
                            if (chart_values instanceof  Array) {
                                var data = new google.visualization.DataTable();
                                var haxisTitle = "Time of Day";
                                data.addColumn('timeofday', 'Time Of Day');
                                if ($scope.coinType == "BTC") {
                                    data.addColumn('number', 'Zebpay Price');
                                    data.addColumn('number', 'Koinex Price');
                                    data.addColumn('number', 'Global Price');
                                    $.each(chart_values, function (i, entry)
                                    {
                                        if (i > 0) {
                                         var hour = Number(entry[0].split(":")[0]);
                                         var min = Number(entry[0].split(":")[1])
                                         var count = entry[1];
                                         var zebpayPrice = Number(count.split(",")[0]);
                                         var koinexPrice = Number(count.split(",")[1]);
                                         var globalPrice = Number(count.split(",")[2]);
                                         data.addRows([[[hour, min, 0], zebpayPrice,koinexPrice,globalPrice]]);
                                     }

                                 });
                                } else {
                                    title = $scope.coinType + " Trend";
                                    data.addColumn('number', $scope.coinType+' Price');
                                    $.each(chart_values, function (i, entry)
                                    {
                                        if (i > 0) {
                                           var hour = Number(entry[0].split(":")[0]);
                                           var min = Number(entry[0].split(":")[1])
                                           var count = entry[1];
                                       // var zebpayPrice = Number(count.split(",")[0]);
                                       var koinexPrice = Number(count.split(",")[0]);
                                       data.addRows([[[hour, min, 0], koinexPrice]]);
                                   }

                               });
                                }
                            // console.log(data);
                            $scope.isChartLoaded= true;
                            $scope.myChartObject.data = data;
                            var options = {
                             title: title,
                             vAxis: {title: $scope.coinType + ' Price', titleTextStyle: {italic: false}},
                             hAxis: {title: haxisTitle, titleTextStyle: {italic: false}},
                         };
                         $scope.myChartObject.options = options;
                         $scope.$apply();
                     } else {
                        $scope.myChartObject.data = {};
                        $scope.isChartDataAvailable = false;
                        $scope.$apply();
                        return;

                    }

                }
                );
                    }
                })

app.controller("myBalanceController", function ($scope, dataService,googleChartApiPromise) {
    doLog("Visited Investment");
    $scope.login=false;
    $scope.isLoading=false;
    $scope.totalInvestment=0;
    $scope.totalVaue=0;
    $scope.profit=0;
    $scope.name="";
    $scope.getMyBalance = function(){
        $scope.isInvalidCredentials= false;
        if($scope.name.trim()=="" || $scope.pin.trim()==""){
            console.log("Wrong");
            doLog("Visited Get Blanace with invalid credentials");
            return false;
        }
        $scope.isLoading = true;
        dataService.getMyBalance($scope.name,$scope.pin).
        then(function(balanceData){
            doLog("Visited Get Blanace:"+$scope.name);
            try{
             $scope.balanceData = JSON.parse(balanceData);
             console.log($scope.balanceData);
             load_coin_wise_chart_data($scope.balanceData);
             var investment = $scope.balanceData.zebpay.total_investments+$scope.balanceData.koinex.total_investments; 
             var profit = $scope.balanceData.zebpay.difference+$scope.balanceData.koinex.difference; 
             if(profit>0){
                 load_investment_vs_profit_chart_data($scope.balanceData);
             }
         }catch(e){
            console.log(e);
            // if(balanceData.trim()=="Invalid Credentials"){
                $scope.login=false;
                $scope.isLoading = false;
                $scope.isInvalidCredentials= true;
                $scope.$apply();
                console.log("Invalid Credentials");
                return;
            // }
        }


        $scope.login=true;
        $scope.isLoading = false;
        // console.log($scope.balanceData);
        $scope.$apply();
    })
    }
    $scope.exchangesOption = ['zebpay','koinex'];
    $scope.coinsOption = ['btc','bchabc','ltc','xrp','eth','trx','bsv'];
    $scope.addNewInvestment = {
        exchangeName: 'zebpay',
        coinType: 'btc',
        investmentAmount: '',
        buyPrice: '',
        bits: '',
        doi: '',
        edit: false
    };

    $scope.addNewInvestment = function () {

        console.log($scope.newInvestment);
        $scope.newInvestment.edit=true;
        console.log($scope.newInvestment);

    }
    $scope.addToMyInvestments = function(){
        console.log($scope.newInvestment);
    }
    /* Graph */
    $scope.myChartObject = {};
    $scope.myChartObject.type = "PieChart";
    $scope.myChartObject.data = {}

    $scope.investmentChart = {};
    $scope.investmentChart.type = "PieChart";
    $scope.investmentChart.data = {}

    $scope.isChartDataAvailable = true;
    $scope.isChart = false;
    function load_coin_wise_chart_data(balanceData) {
        var btcInvestments = balanceData.zebpay.total_investments + balanceData.koinex.btc_total_investments;
        var chart_data_array = [
        ['Coin','Investment'],
        ['BTC',btcInvestments],
        ['ETH',balanceData.koinex.eth_total_investments],
        ['LTC',balanceData.koinex.ltc_total_investments],
        ['XRP',balanceData.koinex.xrp_total_investments],
        ['BCHABC',balanceData.koinex.bchabc_total_investments],
		['BSV',balanceData.koinex.bsv_total_investments],
        ];


        if (chart_data_array instanceof  Array) {
         var data = google.visualization.arrayToDataTable(chart_data_array);

         $scope.isChartLoaded= true;
         $scope.myChartObject.data = data;
         var options = {
             title: 'Coin Share'
                               // vAxis: {title: 'Bitcoin Price', titleTextStyle: {italic: false}},
                               // hAxis: {title: haxisTitle, titleTextStyle: {italic: false}},
                           };
                           $scope.myChartObject.options = options;
                           $scope.isChartDataAvailable = true;
                           $scope.$apply();
                       } else {
                        $scope.myChartObject.data = {};
                        $scope.isChartDataAvailable = false;
                        $scope.$apply();
                        return;

                    }

                }

                function load_investment_vs_profit_chart_data(balanceData) {
                    var investment = balanceData.zebpay.total_investments+balanceData.koinex.total_investments; 
                    var profit = balanceData.zebpay.difference+balanceData.koinex.difference; 
                    var chart_data_array = [
                    ['Investment','Profit'],
                    ['Investment',investment],
                    ['Profit',profit]
                    ];



                    if (chart_data_array instanceof  Array) {
                     var data = google.visualization.arrayToDataTable(chart_data_array);

                     $scope.isChartLoaded= true;
                     $scope.investmentChart.data = data;
                     var options = {
                         title: 'Investment vs Profits'
                               // vAxis: {title: 'Bitcoin Price', titleTextStyle: {italic: false}},
                               // hAxis: {title: haxisTitle, titleTextStyle: {italic: false}},
                           };
                           $scope.investmentChart.options = options;
                           $scope.isChartDataAvailable = true;
                           $scope.$apply();
                       } else {
                        $scope.investmentChart.data = {};
                        $scope.isChartDataAvailable = false;
                        $scope.$apply();
                        return;

                    }

                }
            });


app.controller('userdetailsController', function ($scope) {
    $scope.loading = true;
    $scope.searchkey = '';
    $scope.sortType = ''; // set the default sort type
    $scope.sortReverse = false;  // set the default sort order
    $scope.getUserDetails = function () {
        var data = {
            actionType: 'getUserDetails'
        };
        $.ajax({
            url: 'getUserStats.php',
            method: 'GET',
            success: function (data) {
                dataJson = JSON.parse(data);
                $scope.userDetails = dataJson;
                console.log($scope.userDetails)
                $scope.loading = false;
                $scope.$apply();
            }
        });
    }
    $scope.getUserDetails();
})

app.controller('notificationsController',function($scope){
    console.log("notification controller");
    $scope.loading=true;
    $scope.notificationEnabled = false;
    $scope.dataTags={
        'periodic':"off",
        'spike':false,
        'btc':false,
        'bch':false,
        'eth':false,
        'ltc':false,
        'xrp':true
    }
    OneSignal.push(function() {
      /* These examples are all valid */
      OneSignal.isPushNotificationsEnabled(function(isEnabled) {
        if (isEnabled){
          console.log("Push notifications are enabled!");

          OneSignal.getUserId( function(userId) {
              console.log('player_id of the subscribed user is : ' + userId);
          // Make a POST call to your server with the user ID        
          $scope.playerId = userId;
      });
          OneSignal.getTags().then(function(tags) {
    // All the tags stored on the current webpage visitor
    console.log(tags)
    $scope.dataTags = $scope.convertStringToBoolean(tags);
    $scope.dataTags = $scope.parseFrequency($scope.dataTags);
    console.log($scope.dataTags);
    $scope.loading = false;
    $scope.notificationEnabled=true;
    $scope.$apply();

});
      }
      else{
        console.log("Push notifications are not enabled yet.");   
        $scope.loading = false;
        
    }
});


  });

    $scope.sendDataTags = function(){
        console.log("sendDataTags called ",$scope.dataTags);
            $scope.dataTags.fifteen = false;
            $scope.dataTags.thirty = false;
            $scope.dataTags.fortyfive = false;
            $scope.dataTags.sixty = false;
        /* Handle frequency */
        if($scope.dataTags.periodic=="00"){
            $scope.dataTags.sixty = true;
        }else if($scope.dataTags.periodic=="00,30"){
            $scope.dataTags.sixty = true;
            $scope.dataTags.thirty = true;
        }else if($scope.dataTags.periodic=="00,15,30,45"){
            $scope.dataTags.fifteen = true;
            $scope.dataTags.thirty = true;
            $scope.dataTags.fortyfive = true;
            $scope.dataTags.sixty = true;
        }else if($scope.dataTags.periodic=="off"){
            $scope.dataTags.fifteen = false;
            $scope.dataTags.thirty = false;
            $scope.dataTags.fortyfive = false;
            $scope.dataTags.sixty = false;
        }
        console.log("Frequency Data Parsed ",$scope.dataTags);
        /* Handle frequency End */

        OneSignal.push(function(){
           OneSignal.sendTags({
            periodic: $scope.dataTags.periodic,
            spike: $scope.dataTags.spike,
            btc: $scope.dataTags.btc,
            eth: $scope.dataTags.eth,
            bch: $scope.dataTags.bch,
            ltc: $scope.dataTags.ltc,
            xrp: $scope.dataTags.xrp,
            15: $scope.dataTags.fifteen,
            30:$scope.dataTags.thirty,
            45:$scope.dataTags.fortyfive,
            00:$scope.dataTags.sixty

        },function(tagscallbackdata){
            Materialize.toast('Your settings saved.', 4000) // 4000 is the duration of the toast
            console.log(tagscallbackdata);
        });
       })
    }

    $scope.convertStringToBoolean = function(obj) {
        for(var key in obj) {
            if(obj[key]=="true"){
                obj[key] = true;
            }else{
                obj[key]=false;
            }
        }
        return obj;
    }
    $scope.parseFrequency = function(obj){
        if(obj[15]){
            obj.periodic = "00,15,30,45";   
        }else if(obj[00] && obj[30]){
            obj.periodic = "00,30";   
        }else if(obj[00]){
            obj.periodic = "00";   
        }
        return obj;
    }
})

app.filter('currencyFilter', function() {

  // In the return function, we must pass in a single parameter which will be the data we will work on.
  // We have the ability to support multiple other parameters that can be passed into the filter optionally
  return function(number) {
    try{
        number=number.toString();
    }catch(e){
        return;
    }
    var lastThree = number.substring(number.length-3);
    var otherNumbers = number.substring(0,number.length-3);
    if(otherNumbers != '')
        lastThree = ',' + lastThree;
    var res = otherNumbers.replace(/\B(?=(\d{2})+(?!\d))/g, ",") + lastThree;
    return res;
}

});