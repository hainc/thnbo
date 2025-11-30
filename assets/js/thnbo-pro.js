jQuery(function ($) {
  function post_api(type = 'create') {
    let total = 0;
    let is_over = false;
    var element = layui.element;

    $('.progress-bar-' + type).css('width', '0%');

    $('.thnbo-' + type).hide();
    $('.progress-' + type).show();
    $('.desc-' + type).text('正在执行任务，请勿关闭页面……');

    var i = 1;
    timer = setInterval(function () {
      // 获取一篇文章的ID
      let post_id = 0;
      $.ajax({
        type: 'GET',
        url: thnbo_pro_api.root + 'wp/v2/posts?_fields=id&per_page=1&page=' + i,
        async: false,
        success: function (data) {
          post_id = data[0]['id'];
          if (!post_id) {
            is_over = true;
          }
        },
        error: function (data) {
          is_over = true;
        },
        complete: function (xhr, data) {
          total = xhr.getResponseHeader('x-wp-total');
        }
      });

      $.ajax({
        type: 'POST',
        url: thnbo_pro_api.root + 'wp/v2/posts/' + post_id + '?thnbo_type=' + type,
        beforeSend: function (xhr) {
          xhr.setRequestHeader('X-WP-Nonce', thnbo_pro_api.nonce);
        },
        success: function (data) {
          let progress = Math.round((i / total) * 100);
          console.log(progress)
          $('.progress-bar-' + type).css('width', progress + '%');
        },
        async: false,
      })

      if (is_over) {
        $('.desc-' + type).text('任务执行成功！');
        clearInterval(timer);
      }

      i++;
    }, 100);
  }

  $('.thnbo-create').click(function () {
    post_api('create');
  });

  $('.thnbo-delete').click(function () {
    post_api('delete');
  });
});
