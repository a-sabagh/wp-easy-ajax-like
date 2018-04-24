jQuery(document).ready(function($){
    $(".lj-like-wp").on("click",function(event){        
        event.preventDefault();
        var like_elem = $(this);
        var like_wrapper = $(this).parent(".lj-like-wrapper");
        if(like_elem.hasClass("liked")){
            var like_status = "1";
        }else{
            var like_status = "0";
        }
        $.ajax({
            url: like_obj.admin_url,
            type: "POST",
            data: {
                action: "lj_liked",
                liked: like_status,
                post_id: like_obj.post_id,
                user_id: like_obj.user_id
            },
            beforeSend: function(){
                like_wrapper.append('<span class="lj-loader"></span>');
            },
            success: function(respons){
                if(respons == "add"){
                    var count = parseInt(like_elem.next(".lj-post-like-count").text());
                    like_elem.next(".lj-post-like-count").text(count + 1);
                    like_elem.addClass("liked");
                    like_elem.children("i").removeClass("icon-heart-o");
                    like_elem.children("i").addClass("icon-heart");
                }
                if(respons == "remove"){
                    var count = parseInt(like_elem.next(".lj-post-like-count").text());
                    like_elem.next(".lj-post-like-count").text(count - 1);
                    like_elem.removeClass("liked");
                    like_elem.children("i").removeClass("icon-heart");
                    like_elem.children("i").addClass("icon-heart-o");
                }
                like_wrapper.children(".lj-loader").remove();
            },
            error: function(){
                console.log("error");
            }
        }); 
    });
});

