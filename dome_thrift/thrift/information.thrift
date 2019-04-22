# 具体业务定义
namespace php Information

include "shared.thrift"

struct AdParam{
  1:required shared.TokenParam token
  2:optional shared.PageParam page
}

struct AdItem {
	1:required string title
	4:required string link
}

struct ResultAd {
	1:required i16 Status
  	2:optional list<AdItem> Data
  	3:optional string Message
  	4:optional shared.ResultPage Page
}

service News  extends shared.ShareService
{
	string test(1:string test)throws (1: shared.ResultException e);
	string test1(1:string test)throws (1: shared.ResultException e);
	string test2(1:string test)throws (1: shared.ResultException e);
	ResultAd ad_lists(1:AdParam param)throws (1: shared.ResultException e);
}