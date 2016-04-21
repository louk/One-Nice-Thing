function refresh(id,ele){
    $("#chat_list").addClass("ui form loading");
    $.ajax({
        url : "chat.php",
        type: "POST",
        data : { user:id, check:'get_chatters'},
        success: function(data, textStatus, jqXHR)
    {
        $("#chat_list").removeClass("ui form loading");
        var json = $.parseJSON(data);
        $("#conversations").empty();
        var who = "Me";

        if(json.type==1){

            for(var i = 0; i< json.data.length; i++){
                who = "Me";
                if(json.speakers[i] != u_name){
                    who = json.speakers[i];
                } 
                $("#conversations").append('<div class="comment"><a class="avatar"><img src="'+json.avatar[i]+'"></a><div class="content"><a class="author">'+who+'</a><div class="metadata"><span class="date">'+json.date[i]+'</span></div><div class="text">'+json.data[i]+'</div></div></div>');
            } 
        }
        $("#chat_id").val(json.chat);
    },
        error: function (jqXHR, textStatus, errorThrown)
        {

        }
    });
    $(".section").removeClass("active");
    $(ele).parent().addClass('active');
}

$(function() {
    $('.ui.form.reply').form(message_validate)
    .api({
        action: 'user message',
    method: 'POST',
    serializeForm: true,
    data: {
        name: 22
    },
    beforeSend: function(settings) {
        return settings;
    },
    onSuccess: function(response) {
        $("#conversations").append('<div class="comment"><a class="avatar"><img src="'+u_img+'"></a><div class="content"><a class="author">Me</a><div class="metadata"><span class="date">just now</span></div><div class="text">'+response.message+'</div></div></div>');
        document.getElementById("replay_message").innerHTML = '<textarea name="message" required ></textarea>';
    },
    onFailure: function(response) {
    }
    });
});

$("#direct").click(function(){
    $("#chat_list").addClass("ui form loading");
    $.ajax({
        url : "chat.php",
        type: "POST",
        data : { user:$("#user-id").val(), check:'get_user_direct'},
        success: function(data, textStatus, jqXHR)
    {
        $("#chat_list").removeClass("ui form loading");
        $("#chat_list").empty();
        var json = $.parseJSON(data);
        $("#conversations").empty();
        for (var i = 0; i < json.id.length; i += 1) {
            if ($("#user-id").val() != json.id[i]) {
                $("#chat_list").append('<div class="flex middle section "><a onclick="refresh('+"'"+json.id[i]+"',"+' $(this))" style="cursor: pointer;"><img class="ui avatar image" src="'+json.avatar[i]+'" width="50" height="50"><span>'+json.name[i]+'</span></a><div><div class="flex between"><a href="#"  class="name"></a><a href="#" class="date">never</a></div></div></div>');
            }
        }
    },
        error: function (jqXHR, textStatus, errorThrown)
    {

    }
    });
});

$("#all").click(function(){
    $("#chat_list").addClass("ui form loading");
    $.ajax({
        url : "chat.php",
        type: "POST",
        data : { user:$("#user-id").val(), check:'get_user_all'},
        success: function(data, textStatus, jqXHR)
    {
        $("#chat_list").removeClass("ui form loading");
        var json = $.parseJSON(data);
        $("#chat_list").empty();
        $("#conversations").empty();
        for (var i = 0; i < json.id.length; i += 1) {
            if ($("#user-id").val() != json.id[i]) {
                $("#chat_list").append('<div class="flex middle section "><a onclick="refresh('+"'"+json.id[i]+"',"+' $(this))" style="cursor: pointer;"><img class="ui avatar image" src="'+json.avatar[i]+'" width="50" height="50"><span>'+json.name[i]+'</span></a><div><div class="flex between"><a href="#"  class="name"></a><a href="#" class="date">never</a></div></div></div>');
            }
        }
    },
        error: function (jqXHR, textStatus, errorThrown)
    {

    }
    });
});
function explode(){
    $('.flex.middle.section.active').each(function(i, obj) {
        $(this).children('a').trigger( "click" );
        console.log('asd');
    });
    setTimeout(explode, 60000);
}
setTimeout(explode, 60000);
