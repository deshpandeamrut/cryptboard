app.service('myService', function ($http, $q,$firebaseArray) {
var url = "http://nammabagalkot.in/Angular/process.php";
var url1 = "https://app007.firebaseio.com/";
var ref = new Firebase(url1);
    return {
         
    'all': function() {
        console.log("service");
        var defer = $q.defer();
            $firebaseArray(ref.child('todos')).$loaded().then(function(data){
                defer.resolve(data);
            });
             return defer.promise;
        },
        'getTodos': function () {
            var defer = $q.defer();
            $http.post(url, {'action': 'get'}, {headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }}).success(function (resp) {
                defer.resolve(resp);
            }).error(function (err) {
                defer.reject(err);
            });
            return defer.promise;
        },
        'addTodo': function (todo) {
            var defer = $q.defer();
            $http.post(url, {'action': 'add', 'mydata': todo}, {headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }}).success(function (resp) {
                defer.resolve(resp);
            }).error(function (err) {
                defer.reject(err);
            });
            return defer.promise;
        },
        'removeTodo': function (todo) {
            var defer = $q.defer();
            $http.post(url, {'action': 'delete', 'mydata': todo.id}, {headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }}).success(function (resp) {
                defer.resolve(resp);
            }).error(function (err) {
                defer.reject(err);
            });
            return defer.promise;
        },
        'markTodo': function (todo) {
            var defer = $q.defer();
            $http.post(url, {'action': 'markdone', 'mydata': todo}, {headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                }}).success(function (resp) {
                defer.resolve(resp);
            }).error(function (err) {
                defer.reject(err);
            });
            return defer.promise;
        }
    }
//    var users = [];
//    this.addUser = function (user) {
//        if (users.length == 0) {
//            users.push(user);
//        } else {
//            users.forEach(function (u) {
//                if (u.id == user.id) {
////               alert("present already");
//                    return;
//                } else {
////               alert("added");
//                    users.push(user);
//                }
//            });
//        }
//        localStorage.setItem("userData", JSON.stringify(users));
//
//    };
//    this.getUsers = function () {
//        users = JSON.parse(localStorage.getItem("userData"));
//        return users;
//    }
//    this.popAlert = function (txt) {
//        alert(txt);
//    };
//    var allPosts = [];
//    this.addPosts = function (posts) {
//        if (allPosts.length == 0) {
//            posts.forEach(function (p) {
//                allPosts.push(p);
//            });
//            return 1;
//        }
//        posts.forEach(function (p) {
//            allPosts.forEach(function (p2) {
//                if (p2.id == p.id) {
//                    //already present
//                } else {
//                    allPosts.push(p);
//                }
//            });
//        });
//    };
//    this.getPosts = function (posts) {
//        return allPosts;
//    };
//    this.getPost = function (postId) {
//        var post;
//        allPosts.forEach(function (p) {
//            if (p.id == postId) {
//                post = p;
//                return false;
//            }
//        });
//        console.log(post);
//        return post;
//    }
});


