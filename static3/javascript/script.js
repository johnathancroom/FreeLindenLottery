$(window).load(function() {

  var navigation = $("nav").find("ul"),
      navigationWidth = ($.browser.mozilla || $.browser.msie) ? navigation.width() + 1 : navigation.width();

  navigation.css({ width: navigationWidth, float: "none", margin: "0 auto", visibility: "visible" });
  
  $("#background-radial").css({ visibility: "visible" });

});

$(document).ready(function() {
  
  var contentAnimating = false;

  $("nav").delegate("a", "click", function(e) {
    e.preventDefault()
    
    if(!contentAnimating) {
    
      var contentAnimating = true
      
      $(this).parent("li").siblings().removeClass("active").end().addClass("active");
      
      $("#card-content-wrap").animate({ marginLeft: ($(this).parent("li").index() * 590) * -1 }, 500, "easeOutExpo", function() { contentAnimating = false; });
      
      if($(this).parent("li").index() == 2 && $(".previous table tr").length == 11) // History
      {
        $.ajax({
          url: "/",
          type: "POST",
          data: {
            getPreviousWinners: true
          },
          dataType: "json",
          success: function(data) {
            $.each(data, function(index, piece) {
              var table = $(".previous table");
              tr = $("<tr></tr>").appendTo(table);
              tr.append("<td>"+piece.username+"</td><td>"+piece.claimed+"</td><td>"+piece.prize+"</td><td>"+piece.bonus+"</td><td>"+piece.date+"</td>");
            })
            
            $(".scrollable").data("jsp").reinitialise();
          }
        })
      }
    
    }
  });
  
  $(".scrollable").jScrollPane()
  
  setTimeout(function() {
    $(".flash_error, .flash_notice").slideUp()
  }, 5000)
});

var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-22645449-8']);
_gaq.push(['_trackPageview']);

(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();