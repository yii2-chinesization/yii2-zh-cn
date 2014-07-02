速率限制
=============

为防止滥用，你应该考虑增加速率限制到您的API。
例如，您可以限制每个用户的API的使用是在10分钟内最多100次的API调用。
If too many requests are received from a user within the period of the time, a response with status code 429 (这意味着过多的请求) should be returned.

要启用速率限制, the [[yii\web\User::identityClass|user identity class]] should implement [[yii\filters\RateLimitInterface]].
这个接口需要实现以下三个方法：

* `getRateLimit()`: 返回允许的请求的最大数目及时间，例如，`[100, 600]` 表示在600秒内最多100次的API调用。
* `loadAllowance()`: returns the number of remaining requests allowed and the corresponding UNIX timestamp
  when the rate limit is checked last time.
* `saveAllowance()`: 保存允许剩余的请求数和当前的UNIX时间戳。

You may use two columns in the user table to record the allowance and timestamp information.
And `loadAllowance()` and `saveAllowance()` can then be implementation by reading and saving the values
of the two columns corresponding to the current authenticated user. To improve performance, you may also
consider storing these information in cache or some NoSQL storage.

Once the identity class implements the required interface, Yii will automatically use [[yii\filters\RateLimiter]]
configured as an action filter for [[yii\rest\Controller]] to perform rate limiting check. The rate limiter
will thrown a [[yii\web\TooManyRequestsHttpException]] if rate limit is exceeded. You may configure the rate limiter
as follows in your REST controller classes,

```php
public function behaviors()
{
    $behaviors = parent::behaviors();
    $behaviors['rateLimiter']['enableRateLimitHeaders'] = false;
    return $behaviors;
}
```

当速率限制被激活，默认情况下每个响应将包含以下HTTP头发送
目前的速率限制信息：

* `X-Rate-Limit-Limit`: The maximum number of requests allowed with a time period;
* `X-Rate-Limit-Remaining`: The number of remaining requests in the current time period;
* `X-Rate-Limit-Reset`: The number of seconds to wait in order to get the maximum number of allowed requests.

你可以禁用这些头信息通过配置 [[yii\filters\RateLimiter::enableRateLimitHeaders]] 为false,
就像在上面的代码示例所示。
