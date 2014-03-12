身份验证
==============

身份验证是验证的用户的身份,也是登录操作的基础。通常,身份验证使用一个标识符的组合--用户名或Eamil--及密码。用户通过表单提交这些值,然后应用再与之前存储的资料进行比对(如用户注册时资料)。

在Yii中是半自动执行整个过程的,仅仅让开发人员实现[[yii\web\IdentityInterface]],它是认证系统中最重要的类。通常情况下，`IdentityInterface` 是通过 `User` 模型来实现的。

你可以在[高级应用案例](installation.md)找到一个功能齐全的身份验证的例子. 下面只列出了接口方法:

```php
class User extends ActiveRecord implements IdentityInterface
{
	// ...

	/**
	 * 通过给定的ID找到一个身份。
	 *
	 * @param string|integer $id 需要查找的ID
	 * @return IdentityInterface|null 和给定的ID匹配的身份对象.
	 */
	public static function findIdentity($id)
	{
		return static::find($id);
	}

	/**
	 * 通过给定的令牌找到一个身份。
	 *
	 * @param string $token 需要查找的身份验证密钥
	 * @return IdentityInterface|null 和给定的令牌匹配的身份对象.
	 */
	public static function findIdentityByAccessToken($token)
	{
		return static::find(['access_token' => $token]);
	}

	/**
	 * @return int|string 当前用户ID
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string 当前用户的身份验证密钥
	 */
	public function getAuthKey()
	{
		return $this->auth_key;
	}

	/**
	 * @param string $authKey
	 * @return boolean 当前用户的身份验证密钥是否有效
	 */
	public function validateAuthKey($authKey)
	{
		return $this->getAuthKey() === $authKey;
	}
}
```

两个纲要方法很简单： `findIdentity`接受一个ID值，并返回与该ID相关联的模型实例。 `getId` 方法则返回ID本身。
两个其他方法--`getAuthKey` 和 `validateAuthKey`--用于提供“保持登录状态(remember me)”的cookie的额外安全性。`getAuthKey` 方法应该返回一个字符串，对于每个用户它都是唯一的。您可以用 `Security::generateRandomKey()` 可靠地创建一个唯一的字符串。将这字符串保存为用户的一个字段是一个不错的做法：

```php
public function beforeSave($insert)
{
	if (parent::beforeSave($insert)) {
		if ($this->isNewRecord) {
			$this->auth_key = Security::generateRandomKey();
		}
		return true;
	}
	return false;
}
```

`validateAuthKey` 方法需要将传入的 `$authKey` 变量（从一个cookie中获得）与数据库中的数据进行比较。(The `validateAuthKey` method just needs to compare the `$authKey` variable, passed as parameter (itself retrieved from a cookie), with the value fetched from database.)
