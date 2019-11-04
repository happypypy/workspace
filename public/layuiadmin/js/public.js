

function GetOpenerWin() {
    return window.parent
}

//打开层
function CustomOpen(url, code,showName, w,h)
{
  layer.open({
	  type: 2,
	  title: showName,
      shade: 0.8,
	  maxmin: true,
	  area: [w, h],
	  area: [w+'px', h+'px'],
	  content: url
  });
}

//选择时间
function seltime(id,DateType)
{
	if(DateType==undefined)
	{
		//year,month,date,time,datetime	
		DateType='date';
	}
	laydate.render({
        elem: '#'+id
		,type:DateType
        , min: '1919-01-01'
    });
}

//选择时间范围
var date_key=[];
function seltime1(id,id1,DateType)
{
	if(DateType==undefined)
	{
		//year,month,date,time,datetime	
		DateType='date';
	}
	date_key[id] = laydate.render({
		elem: '#'+id
		, min:'1919-01-01'
        ,type:DateType
		, done: function (value, date) {
		  //更新结束日期的最小日期
		  date_key[id1].config.min = lay.extend({}, date, {
			month: date.month - 1
		  });
		  //自动弹出结束日期的选择器
		  date_key[id1].config.elem[0].focus();
		}
	});
	//发布开始日期
	date_key[id1] = laydate.render({
		elem: '#'+id1
		, min: '1919-01-01'
        ,type:DateType
		, done: function (value, date) {
		  //更新开始日期的最大日期
		  date_key[id].config.max = lay.extend({}, date, {
			month: date.month - 1
		  });
		}
	});
}

//截图
function uploadimgcut(elementid,path,w,h)
{
    var upurl ='/index.php/admin/Uploadify/uploadimgcut/input/'+elementid+'/w/'+w+'/h/'+h+'/path/'+path;
    CustomOpen(upurl,elementid, '文件上传', '775', '577');
}

 /**
  * 修改指定表的指定字段值
  * table表名
  * id_name表主键字段
  * id_value表id
  * field要修改的字段
  * obj
 * */
 function changeTableVal(table,id_name,id_value,field,obj)
 {
     var src = "";

     if($(obj).attr('src').indexOf("cancel.png") > 0 )
     {
         src = '/static/images/yes.png';
         var val= 1;
     }else{
         src = '/static/images/cancel.png';
         var val = 2;
     }
     $.ajax({
         url:"/Admin/Pattern/changeTableVal/table/"+table+"/id_name/"+id_name+"/id_value/"+id_value+"/field/"+field+'/value/'+val,
         success: function(data){
             $(obj).attr('src',src);
             if(data == 1){
                 layer.alert("修改成功",{icon:1});
             }else {
                 layer.alert("修改失败",{icon:2});
             }
         }
     });
 }

//获取城市
function getChild(t,selected,child_id,url1){
    var parent_id = t;
    if(!parent_id > 0){
        //return;
    }
    $('#twon').empty().css('display','none');
    var url = url1+'/level/2/parent_id/'+ parent_id+"/selected/"+selected;
    $.ajax({
        type : "GET",
        url  : url,
        error: function(request) {
            alert("服务器繁忙, 请联系管理员!");
            return;
        },
        success: function(v) {
            v = '<option value="0">请选择</option>'+ v;
            $('#'+child_id).empty().html(v);
        }
    });

}
      