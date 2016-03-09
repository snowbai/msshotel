<input type="text" name="username" id="username">
<input type="text" name="password" id="password">
<input type="text" name="verifycode" id="verifycode"><img id="yzm" src="/login/yzm" onclick="changeYzm();">
<input type="button" value="提交" onclick="login();">
<script type="text/JavaScript" src=<?php echo FRONT_PUBLIC;?>common/js/jquery-2.1.4.min.js></script>
<script type="text/JavaScript">
    function changeYzm(){
        $.ajax({
          type:"get",
          url:"/login/yzm?refresh=1",
          dataType:"json",
          success:function(data){
            console.log(data.url);
            $("#yzm").attr("src",data.url);
          }
        });
    }
    function login(){
      $.ajax({
        type:"post",
        data:{username:$("#username").val(),password:$("#password").val(),verifycode:$("#verifycode").val()},
        url:"/login/login",
        dataType:"json",
        success:function(data){
            if(data.isSuccess==true){
                //console.log(data.data.url);
                window.location.href=data.data.url;
            }
        }
      });
    }
</script>
