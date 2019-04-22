//公共接口定义
namespace php Shared

//php异常就是下面属性
//业务逻辑出错时候,使用异常输出,直接抛异常,server处理层有异常处理
exception ResultException{
	1:required i16 code
  	2:required string message
  	3:required string file
  	4:required string line
}
//页码参数
struct PageParam{
  1:optional i32 page=1
  2:optional i32 offset=0
  3:optional i32 show=0
}
//一般用来请求验证
struct TokenParam{
  1:required string token 
  2:required string time
  3:optional i16 type=0
  4:optional i16 platform=1
}

//页码请求
struct ResultPage{
	/**
	* 页码
	*/
  1:optional i32 page=1
  /**
   * 偏移
   */
  2:optional i32 offset=0
  /**
   * 显示数量
   */
  3:optional i32 show=0
}

//返回状态,如添加数据等返回成功,进行中或其他
struct ResultStatus{
  1:required i32 status
  2:optional i32 msg
}


service ShareService 
{
	
}

