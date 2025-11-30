layui.use(['form', 'element','jquery'], function() {
  var $ =layui.jquery;
  var form = layui.form;
  function menuFixed(id) {
    var obj = document.getElementById(id);
    var _getHeight = obj.offsetTop;
    var _Width= obj.offsetWidth
    window.onscroll = function () {
      changePos(id, _getHeight,_Width);
    }
  }
  function changePos(id, height,width) {
    var obj = document.getElementById(id);
    obj.style.width = width+'px';
    var scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
    var _top = scrollTop-height;
    if (_top < 150) {
      var o = _top;
      obj.style.position = 'relative';
      o = o > 0 ? o : 0;
      obj.style.top = o +'px';

    } else {
      obj.style.position = 'fixed';
      obj.style.top = 50+'px';

    }
  }
  menuFixed('nav');

  var laobueys = $('.laobuluo-wp-hidden')

  laobueys.each(function(){

    var inpu = $(this).find('.layui-input');
    var eyes = $(this).find('.laobuluo-wp-eyes')
    var width = inpu.outerWidth(true);
    eyes.css('left',width+'px').show();

    eyes.click(function(){
      if(inpu.attr('type') == "password"){
        inpu.attr('type','text')
        eyes.html('<i class="dashicons dashicons-visibility"></i>')
      }else{
        inpu.attr('type','password')
        eyes.html('<i class="dashicons dashicons-hidden"></i>')
      }
    })
  })

  var  clashid = $(".clashid");
  form.on('switch(process_switch)', function(data){
    if (data.elem.checked){
      clashid.show()
    }else{
      clashid.hide()
    }
  });

  var selectValue = null;

  var rule = $("[name=img_process_style_customize]")

  form.on('radio(choice)', function(data){

    if(selectValue == data.value && selectValue ){
      data.elem.checked = ""
      selectValue = null;
    }else{
      selectValue = data.value;
    }

    if(selectValue=='1'){
      rule.attr('disabled',false)
    }else{
      rule.attr('disabled', true)
    }

  })

})
