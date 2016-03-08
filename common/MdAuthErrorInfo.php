<?php

namespace common;

/**
 * auth Exception INFO description
 */
class MdAuthErrorInfo
{
    //hotelmember auth Exception
    const HOTEL_MEMBER_LOCALAUTH_WRONGPHONE = 1001;//手机号不存在
    const HOTEL_MEMBER_LOCALAUTH_WRONGPWD = 1002;//手机号或者密码错误

    const HOTEL_MEMBER_OAUTH_WRONGOPENID = 1003;//错误的openid
    const HOTEL_MEMBER_OAUTH_TOKENVALID = 1004;//token失效
    const HOTEL_MEMBER_OAUTH_NOMEMBER = 1005;//未绑定账号

    const HOTEL_MEMBER_OAUTH_NOTAUTOLOGIN = 1006;//无需自动登录

    const MEMBER_NOT_LOGIN = 2001;//MSS未登录
    const LOGIN_MEMBER_NOT_THIS_HOTEL = 2002;//MSS登陆账号不是该酒店账号
}
