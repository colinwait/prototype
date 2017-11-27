var totalheight = 0;

function loadNewSuite(container, data) {
    jQuery.ajax({
        type: "GET",
        url: "/aisi/new",
        data: data,
        dataType: "json",
        beforeSend: function (XMLHttpRequest) {
            $("#loading").css('display', '');
            container.find('#is_ajax').text(1);
        }, success: function (response) {
            if (response.suites) {
                for (var i = 0, length = response.suites.length; i < length; i++) {
                    var suite = response.suites[i];
                    var test =
                        '<div class="float-left">' +
                        '<a href="/aisi/pic/' + suite.issue + '?count=' + suite.pics + '&catalog=' + suite.source.catalog + '">' +
                        '<div class="img-div">' +
                        '<img src="' + suite.url + '" class="blur">' +
                        '<p>' + suite.name + '</p>' +
                        '<p>图片总数 ' + suite.pics + '</p>' +
                        '</div>' +
                        '</a>' +
                        '</div>';
                    container.append(test);
                }
                container.find('#next_page').text(response.next_page);
                $("#loading").css('display', 'none');
                container.find('#is_ajax').text(0);
            }
        }, error: function () {
            alert("加载失败");
        }
    });
}

function loadSuite(id, container, data) {
    data.suits = parseInt(container.find('#suits').text());
    console.log(data);
    jQuery.ajax({
        type: "GET",
        url: "/aisi/" + id,
        data: data,
        dataType: "json",
        beforeSend: function (XMLHttpRequest) {
            $("#loading").css('display', '');
            container.find('#is_ajax').text(1);
        }, success: function (response) {
            if (response.suites) {
                for (var i = 0, length = response.suites.length; i < length; i++) {
                    var suite = response.suites[i];
                    var test =
                        '<div class="float-left">' +
                        '<a href="/aisi/pic/' + suite.issue + '?count=' + suite.pics + '&catalog=' + suite.source.catalog + '">' +
                        '<div class="img-div">' +
                        '<img src="' + suite.url + '" class="blur">' +
                        '<p>' + suite.name + '</p>' +
                        '<p>图片总数 ' + suite.pics + '</p>' +
                        '</div>' +
                        '</a>' +
                        '</div>';
                    container.append(test);
                }
                container.find('#next_page').text(response.next_page);
                $("#loading").css('display', 'none');
                container.find('#is_ajax').text(0);
            }
        }, error: function () {
            alert("加载失败");
        }
    });
}

function loadData() {
    totalheight = parseFloat($(window).height()) + parseFloat($(window).scrollTop());

    if ($(document).height() <= totalheight) {  // 说明滚动条已达底部
        /*这里使用Ajax加载更多内容*/
        var container = $("#pic_container"); // 加载容器
        var data = {}; // 查询参数
        // 当前页
        var next_page = parseInt(container.find('#next_page').text());
        var is_ajax = parseInt(container.find('#is_ajax').text());
        var id = parseInt(container.find('#id').text());
        if (is_ajax) {
            return;
        }
        data.page = next_page;
        data.api = 1;
        if (id) {
            loadSuite(id, container, data);
        } else {
            loadNewSuite(container, data);
        }
    }
}

$(window).scroll(function () {
    loadData();
});
