---
title: 介绍
lang: zh-CN
---

# 介绍

## 访问令牌（Access Token）

它的有效期比较短，一般在15分钟到1小时之间。
主要用途是访问那些受保护的API资源。
每次我们向API发起请求的时候，都要在请求头里带上它，格式通常是 `Authorization: Bearer <token>`。
因为它的有效期短，就算不小心泄露了，风险也相对没那么大，毕竟很快就会过期失效。

## 刷新令牌（Refresh Token）

它的寿命要长得多，可能是7天、30天，甚至更久。
它只有一个作用，就是用来获取新的 `Access Token`。

::: warning
千万要记住，不能在访问普通API的时候携带它，只有当 `Access Token` 过期了，我们调用专门的刷新接口时才会用到它。
:::

::: tip
在前端存储 `Refresh Token` 的时候，要选择更安全的方式，比如存在 `localStorage`、`sessionStorage` 里， 或者使用更安全的 HttpOnly `Cookie` 来存储。
:::

## 授权码（Authorization Code）

授权码核心用途：

- 临时授权：用户授权后，第三方应用可以访问用户的信息，比如名称、头像、邮箱等等。
- 解除账户限制：账户被限制时，可以使用授权码来解除限制。

## 认证流程

![认证流程](/assets/zh/auth.svg)

- 用户操作：用户在前端输入账号和密码，点击登录；
- 前端处理：前端把用户输入的凭据发送到后端的登录接口；
- 后端处理：后端接收到请求后，会验证用户的凭据。如果验证成功，就会生成一个短寿命的 `Access Token` 和一个长寿命的 `Refresh Token`；要是验证失败，就返回错误信息；
- 返回结果：验证成功后，后端把 `Access Token` 和 `Refresh Token` 返回给前端；验证失败时，返回 *401 Unauthorized* ，前端接收到后显示错误消息；
- 前端存储：前端接收到 `Access Token` 和 `Refresh Token` 后，将它们安全地存储起来，登录成功。

## 访问受保护资源

- 前端请求：前端向需要授权的API发起请求，在请求头里带上 `Authorization: Bearer <Access Token>`；
- 后端验证：后端收到请求后，会对 `Access Token` 进行验证，主要检查它的签名是否有效、有没有过期，以及权限范围是否符合要求；
- 返回结果：如果 `Access Token` 有效，后端就正常处理请求，返回业务数据，前端接收到数据后正常显示；要是 `Access Token` 无效或者过期了，后端会返回 *401 Unauthorized*。

## 刷新令牌流程

![刷新令牌流程](/assets/zh/refresh.svg)

- 前端拦截：前端通过配置HTTP响应拦截器来捕获所有的响应。当拦截器捕获到后端返回的 *401 Unauthorized* 错误，并且确认是 `Access Token` 过期导致的，将当前失败的请求放入失败队列；
- 检查刷新令牌：拦截器会检查本地是否存储了 `Refresh Token`。如果有，就向后端特定的刷新接口 `/api/auth/refresh` 发送请求，并在请求头里带上 `Authorization: Bearer <Refresh Token>`；
- 后端验证：后端接收到刷新请求后，会验证 `Refresh Token` 的有效性；
- 返回结果：如果 `Refresh Token` 有效，后端会生成新的 `Access Token`和 `Refresh Token`，然后返回给前端；要是 `Refresh Token` 无效或过期，后端返回错误（比如401或403 ），前端收到错误后，清除本地存储的所有令牌，然后把用户重定向到登录页面；
- 重放请求：前端接收到新的令牌后，更新本地存储的令牌，并用新的 `Access Token` 重新执行失败队列中的请求。
