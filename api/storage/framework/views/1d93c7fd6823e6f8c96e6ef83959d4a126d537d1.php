<script type="text/javascript" src="/public/static/non_bower_components/bootstrap.min.js"></script>
<script type="text/javascript" src="/public/static/non_bower_components/application.js"></script>
<script type="text/javascript" src="/public/static/non_bower_components/ddsmoothmenu.js"></script>
<script type="text/javascript" src="/public/static/non_bower_components/wow.min.js"></script>
<script type="text/javascript">
	ddsmoothmenu.init({
        mainmenuid: "smoothmenu1", //menu DIV id
        orientation: 'h', //Horizontal or vertical menu: Set to "h" or "v"
        classname: 'navhor', //class added to menu's outer DIV
        //customtheme: ["#1c5a80", "#18374a"],
        contentsource: "markup" //"markup" or ["container_id", "path_to_menu_file"]
    })

    $(document).scroll(function() {
        var curPos = $(document).scrollTop();
        if (curPos >= 350) {
            $('#fixedmenu').show(10);
        } else {
            $('#fixedmenu').hide(10);
        }
    });
    $('.scrollup').click(function() {
        $("html, body").animate({
            scrollTop: 0
        }, 600);
        return false;
    });

    function displaymenu() {
        var temp = document.getElementById('input_tem').value;
        if (temp == '0') {
            $('#input_tem').val('1');
            $('.menu-respon').addClass('menu-respon-active');
        } else {
            $('.menu-respon').removeClass('menu-respon-active');
            $('#input_tem').val('0');
        }
    }
    function loading() {
        $('#loading_geturl').show();
    }

	new WOW().init();
</script>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-90269105-1', 'auto');
  ga('send', 'pageview');
</script>
<script type="text/javascript">
    /* <![CDATA[ */
    var google_conversion_id = 987316405;
    var google_custom_params = window.google_tag_params;
    var google_remarketing_only = true;
    /* ]]> */
</script>
<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
    <div style="display:inline;">
    <img height="1" width="1" style="border-style:none;" alt="" src="//googleads.g.doubleclick.net/pagead/viewthroughconversion/987316405/?guid=ON&amp;script=0"/>
    </div>
</noscript>

