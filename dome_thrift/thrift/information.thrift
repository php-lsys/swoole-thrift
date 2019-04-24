# 具体业务定义
namespace php DomeInformation

include "shared.thrift"

struct DomeAdParam{
  1:required shared.DomeTokenParam token
  2:optional shared.DomePageParam page
}

struct DomeAdItem {
	1:required string title
	4:required string link
}

struct DomeResultAd {
	1:required i16 Status
  	2:optional list<DomeAdItem> Data
  	3:optional string Message
  	4:optional shared.DomeResultPage Page
}

service DomeNews  extends shared.DomeShareService
{
	string test(1:string test)throws (1: shared.DomeResultException e);
	string test1(1:string test)throws (1: shared.DomeResultException e);
	string test2(1:string test)throws (1: shared.DomeResultException e);
	DomeResultAd ad_lists(1:DomeAdParam param)throws (1: shared.DomeResultException e);
}