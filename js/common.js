$(document).ready(function(){

    $(document).ajaxStart(function() { Pace.restart(); });

	if ($(".flash-message").find('p').html() != null 
			&& 
		$(".flash-message").find('p').html().length != 0) {
        
        $(".flash-message").removeClass("hide");
        
        setTimeout(function() {
            $(".flash-message").fadeOut("slow")
        }, 5000);
    }

    $("#go-back").on("click", function(){
        if(window.location.pathname!="/user/index")
            window.history.go(-1);
    });

    $(".notifications-menu").on("click", function(e){
        var list = [];
        var headCount = $(".notifications-menu").find(".dropdown-toggle").find(".label-warning");
        $(this).find(".menu li").each(function(){
            list.push($(this).attr("data-nid"));
        });

        if(list.length > 0 && parseInt(headCount.text()) > 0) {
            var ids = list.join(",");
            var url = "/ajax/read_alerts?ids=" + ids;
            $.ajax({url: url, dataType: "json",
                success: function(result){
                    $(".notifications-menu").find(".dropdown-toggle").find(".label-warning").addClass("hide").text(0);
                },
                complete: function(){}
            });
        }
    });

    function pullAlerts(){
        console.log("Calling pull alerts");
        
        var user_id = $(".notifications-menu").attr("data-lid");
        var url = "/ajax/pull_alerts?lid=" + user_id;
        var element = $(".notifications-menu").find(".dropdown-menu").find(".menu");
        var headCount = $(".notifications-menu").find(".dropdown-toggle").find(".label-warning");
        var header = $(".notifications-menu").find(".dropdown-menu").find(".header");

        $.ajax({url: url, dataType: "json",
            success: function(result){
                if(result.alerts.length > 0 ){
                    var count = result.count;
                    $(".notifications-menu").attr("data-lid", result.alerts[0].id);
                    for(var i = 0; i < result.alerts.length; i++){
                        element.prepend("<li data-nid='"+result.alerts[i].id+"'>" + result.alerts[i].content + "<span class='notification-time'>" + result.alerts[i].created_on + "</span></li>");
                    }
                    headCount.text(count);
                    
                    if(count > 1)
                        header.text("You have "+ result.alerts.length +" notifications");
                    else
                        header.text("You have "+ result.alerts.length +" notification");

                    if(count == 0)
                        headCount.addClass("hide");
                    else 
                        headCount.removeClass("hide");
                }
            },
            complete: function(){
                setTimeout(function() {
                    pullAlerts();
                }, 25000);
            }
        });
    }

    function pullFeeds(){
        console.log("Calling pull feeds");
        
        var last_id = $(".control-sidebar-menu").attr("data-lid");
        var url = "/ajax/pull_feeds?lid=" + last_id;
        var element = $(".control-sidebar-menu");

        $.ajax({url: url, dataType: "json",
            success: function(result){
                if(result.feeds.length > 0){
                    $(".control-sidebar-menu").attr("data-lid", result.feeds[0].id);
                    for(var i = 0; i < result.feeds.length; i++){
                        element.prepend("<li>" + result.feeds[i].content + "<span class='notification-time'>" + result.feeds[i].created_on + "</span></li>");
                    }
                }
            },
            complete: function(){
                setTimeout(function() {
                    pullFeeds();
                }, 25000);
            }
        });
    }

    pullAlerts();
    pullFeeds();
});