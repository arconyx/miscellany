!function(e){function t(i){if(n[i])return n[i].exports;var r=n[i]={i:i,l:!1,exports:{}};return e[i].call(r.exports,r,r.exports,t),r.l=!0,r.exports}var n={};t.m=e,t.c=n,t.d=function(e,n,i){t.o(e,n)||Object.defineProperty(e,n,{configurable:!1,enumerable:!0,get:i})},t.n=function(e){var n=e&&e.__esModule?function(){return e.default}:function(){return e};return t.d(n,"a",n),n},t.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},t.p="",t(t.s=5)}({5:function(e,t,n){e.exports=n("O+W/")},"O+W/":function(e,t){function n(){h&&(h=!1,d=$(".entity-files-drop"),c=$("#entity-file-progress"),f=$(".entity-file-upload-max"),d.bind("drop",function(e){e.preventDefault(),c.show()}).on("click",function(e){$("#entity-file-upload").trigger("click")}),$.ajaxSetup({headers:{"X-CSRF-TOKEN":$('meta[name="csrf-token"]').attr("content")}}),$("#entity-file-upload").fileupload({dropZone:d,dataType:"json",add:function(e,t){d.hide(),t.submit()},progressall:function(e,t){var n=parseInt(t.loaded/t.total*100,10);$(".progress").show(),$(".progress .progress-bar").css("width",n+"%")},done:function(e,t){$(".progress").hide(),t.result.success?(a(t.result),s(t.result.enabled),$(".entity-file-upload-error").hide()):$(".entity-file-upload-error").text(t.result.error).fadeToggle()}}),d.bind("drop",function(e){c.show()}))}function i(){$(".entity-file-remove").each(function(){$(this).unbind("click"),$(this).on("click",function(e){$(this).removeClass("fa-trash").addClass("fa-spinner").addClass("fa-spin"),$.post({url:$(this).data("url"),data:{_method:"DELETE"},context:this}).done(function(e,t,n){$(this).parent().fadeOut(),s(e.enabled)})})})}function r(){$(".entity-file-rename").each(function(e){$(this).unbind("click").on("click",function(e){$(this).parent().children("a").hide(),$(this).parent().children("input").val($(this).data("default")).show().focus(),$(this).hide()})})}function o(){$(".entity-file-name").each(function(){$(this).unbind("keypress").unbind("focusout").keypress(function(e){var t,n=e.keyCode||e.which;13===n&&(e.preventDefault(),t=$(this).parent().children("a"),$.post({url:$(this).data("url"),data:{_method:"PATCH",name:$(this).val(),"csrf-token":$(".csrf-token").val()},datatype:"JSON",context:this}).done(function(e){var n=$(this).val();$(this).val(n).hide(),t.data("default",n).html(n).show(),$(this).parent().children(".entity-file-rename").data("default",n).show(),$(".entity-file-error").hide()}).fail(function(e){var t="";for(var n in e.responseJSON.errors)e.responseJSON.errors.hasOwnProperty(n)&&(t+=e.responseJSON.errors[n]+"\n");$(this).parent().children(".entity-file-error").text(t).show()}))}).focusout(function(e){link=$(this).parent().children("a"),$(this).val($(this).data("default")),link.show(),$(this).hide(),$(this).parent().children(".entity-file-rename").show(),$(".entity-file-error").hide()})})}function a(e){$(".entity-files").html(e.html),i(),r(),o()}function s(e){e?(d.fadeIn(),f.hide()):(d.hide(),f.fadeIn())}var l,u,d,c,f,h=!1;$(document).ready(function(){l=$(".entity-file-ui"),u=$("#entity-modal"),1===l.length&&l.on("click",function(e){h=!0,u.on("shown.bs.modal",function(e){n(),i(),r(),o()})})})}});