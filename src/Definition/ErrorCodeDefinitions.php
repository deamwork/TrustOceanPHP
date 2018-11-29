<?php
/**
 * trustocean
 * Component:
 * File: ErrorCodeDefinitions.php
 * Author: jason
 * Time: 11/29/18 1:47 AM
 */

namespace TrustOcean\Definition;

class ErrorCode extends Definitions
{

    // General, start with 7788xx
    const MISSING_PARAM                = 'API 请求缺少特定的参数'; // 01
    const EXCESS_PARAM                 = 'API 请求中包含了多余的参数'; // 02
    const MISSING_AUTHORIZE            = 'API 参数中缺少 username 或 password 参数'; // 03
    const NOT_AUTHORIZE                = '用户名或密码错误'; // 04
    const NOT_PARTNER                  = '当前账户未获得合作伙伴 API 访问权限，请联系账户经理开通或登录 TRUSTOCEAN 控制台设置'; // 05
    const NOT_WHITELISTED              = '访问 API 的客户端/服务器的公网 IPv4 地址不在 API 账户的 IP 地址白名单中，请登录 TRUSTOCEAN 控制台添加'; // 06
    const NOT_ALLOWED_OR_MISSING_ORDER = '没有权限访问当前 API 中的订单(trustocean_id)，订单不存在或不属于当前合作伙伴账户'; // 07

    // Products, start with 7700xx
    // Place new order
    const EXCEED_DOMAIN       = '多域名证书订单中的 domain_count 超出了最大限制(250 条)'; // 02
    const LACK_OF_DOMAIN      = '多域名证书订单中，domain_count 需要大于等于 3'; // 03
    const INSUFFICIENT_CREDIT = '合作伙伴账户余额不足，订单已作废'; // 04
    const FAILED_CREATE_ORDER = '合作伙伴账户可能出现异常，无法创建新的订单，请登陆控制台或联系账户经理检查账户后重试'; // 05

    // Upload CSR
    const ORDER_REJECTED_DOMAIN_LIST = '当前订单状态不支持上传域名列表'; // 06
    const CN_REJECTED_BY_CA          = '提交的域名列表中包含格式不正确的域名，错误域名在错误信息列出'; // 07

    // Upload Domains
    const CSR_REJECTED_BY_CA = '订单在提交到 CA 时校验出现错误，请查看错误信息详细披露'; // 08
    const CSR_CN_REJECTED    = 'CSR 中的 Common Name 域名必须是 FQDN 名称，不能为 IP 地址或其他非法字符'; // 24

    // Add SANs
    const CERT_NOT_SAN            = '当前证书不是多域名证书，无法添加域名'; // 09
    const EMPTY_DOMAIN            = '输入的域名数量不合法，必须大于 0'; // 10
    const EXCEED_CERT_SANS        = '订单域名总数超过最大限制 250 条，请修改 newsan 数量'; // 11
    const NEED_PAY_PREVIOUS_ORDER = '该订单存在未支付的 SAN 域名账单，请登录 TRUSTOCEAN 控制台完成支付后才可继续添加'; // 12
    const FAILED_CREATE_SAN_ORDER = '合作伙伴账户异常，无法创建 SAN 域名账单，请联系账户经理或稍后再试'; // 13
    const FAILED_PROCESS_DOMAIN   = '处理域名信息失败，请联系我们检查您的订单'; // 14

    // Sync get order details
    const NOT_SUBMITTED = '当前订单暂未提交到 CA，或此方法暂时不可用'; // 15

    // Sync change DCV
    const VERIFY_CHANGE_REJECTED = '当前订单可能已经签发或状态不允许修改 DCV 验证方式，请查看 message 报告'; // 16

    // Sync remove domain
    const NOT_ALLOWED_DOMAIN_DELETION = '当前操作的订单不支持删除域名，仅支持从多域名证书中删除域名'; // 17
    const FAILED_DELETE_DOMAIN_CA     = '从 CA 处删除域名失败，详细信息请参考 message 信息'; // 18

    // Sync resend DCV email / redo DCV check
    const FAILED_VERIFY = '从 CA 处执行 DCV 邮件重发或验证域名失败，可能是证书已经签发，可能是证书状态不允许执行该操作'; // 19

    // set certificate reissue, start with 7700xx
    const NOT_ISSUED = '当前订单状态不支持此操作，只有 issued_active 状态的订单才可以设置为重签状态'; // 20

    // upload organization details
    const REQUIRE_SPECIFIC_ORDER = '当前证书状态无法上传企业信息，只有 enroll_organization 状态的订单才可以上传企业信息'; // 21
    const REQUIRE_OV_EV          = '当前证书无法上传企业信息，只有 OV\EV 订单才可以上传企业信息'; // 22
    const REQUIRE_MORE_INFO      = '企业信息上传不全，请参考请求参数列表'; // 23


    public function __construct()
    {
        parent::__construct();
    }

    public static function define(int $error_code)
    {
        switch ($error_code) {
            // General
            case 778801:
                return self::MISSING_PARAM;
            case 778802:
                return self::EXCESS_PARAM;
            case 778803:
                return self::MISSING_AUTHORIZE;
            case 778804:
                return self::NOT_AUTHORIZE;
            case 778805:
                return self::NOT_PARTNER;
            case 778806:
                return self::NOT_WHITELISTED;
            case 778807:
                return self::NOT_ALLOWED_OR_MISSING_ORDER;

            // Place Order
            case 770002:
                return self::EXCEED_DOMAIN;
            case 770003:
                return self::LACK_OF_DOMAIN;
            case 770004:
                return self::INSUFFICIENT_CREDIT;
            case 770005:
                return self::FAILED_CREATE_ORDER;

            // Upload CSR
            case 770006:
                return self::ORDER_REJECTED_DOMAIN_LIST;
            case 770007:
                return self::CN_REJECTED_BY_CA;

            // Upload Domains
            case 770008:
                return self::CSR_REJECTED_BY_CA;
            case 770024:
                return self::CSR_CN_REJECTED;

            // Add SANs
            case 770009:
                return self::CERT_NOT_SAN;
            case 770010:
                return self::EMPTY_DOMAIN;
            case 770011:
                return self::EXCEED_CERT_SANS;
            case 770012:
                return self::NEED_PAY_PREVIOUS_ORDER;
            case 770013:
                return self::FAILED_CREATE_SAN_ORDER;
            case 770014:
                return self::FAILED_PROCESS_DOMAIN;

            // Sync get order details
            case 770015:
                return self::NOT_SUBMITTED;

            // Sync change DCV
            case 770016:
                return self::VERIFY_CHANGE_REJECTED;

            // Sync remove domain
            case 770017:
                return self::NOT_ALLOWED_DOMAIN_DELETION;
            case 770018:
                return self::FAILED_DELETE_DOMAIN_CA;

            // Sync resend DCV email / redo DCV check
            case 770019:
                return self::FAILED_VERIFY;

            // set certificate reissue
            case 770020:
                return self::NOT_ISSUED;

            // upload organization details
            case 770021:
                return self::REQUIRE_SPECIFIC_ORDER;
            case 770022:
                return self::REQUIRE_OV_EV;
            case 770023:
                return self::REQUIRE_MORE_INFO;
        }
    }
}
