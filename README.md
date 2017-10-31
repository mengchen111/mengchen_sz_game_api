# 管理后台
## 环境依赖
- php >= 5.6  
- nginx打开ssi  
- redis >= 2.8
- composer  
- supervisor  

```
supervisor配置文件模版：
[program:laravel-queue-worker]
process_name=%(program_name)s_%(process_num)02d
directory={code_ducument_root}   ;低版本不支持此指令
command=/usr/bin/php {code_ducument_root}/artisan queue:work --delay=3 --sleep=1 --tries=3 --timeout=60
autostart=true
autorestart=true
startretries=3
user=nginx
numprocs=1
redirect_stderr=true
stdout_logfile=/data/log/supervisor/%(program_name)s.log
stdout_logfile_maxbytes=100MB
stdout_logfile_backups=10
```  
- node & npm

```
安装node和npm环境：
curl --silent --location https://rpm.nodesource.com/setup_8.x | sudo bash -
yum -y install nodejs
```

## 单元测试与代码覆盖率
测试并生成代码覆盖率报表：  
```
cd ${code_ducument_root}
./vendor/bin/phpunit --coverage-html public/test/
```
查看代码覆盖率：
```
URI: /test/index.html
```

## 生产环境代码发布  

```
cd ${code_ducument_root}
git pull                #获取最新代码
composer install        #安装laravle依赖
cp .env.example .env    #配置文件(根据生产环境配置对应的参数)
php artisan storage:link    #创建符号链接到文件上传目录
chmod -R {phpfpm_runner}.{phpfpm_runner} ./ #更改代码目录的权限为phpfpm程序的运行用户
chmod +x vendor/phpunit/phpunit/phpunit #添加执行权限
./vendor/bin/phpunit    #代码测试

cd client       #进入js开发目录
npm install     #安装npm包
npm run build   #编译js代码
```  

### cron计划任务
```
crontab -e
* * * * * php {code_ducument_root}/artisan schedule:run >> /dev/null 2>&1  

#注意：.env里面正确配置好日志输出文件"CRON_TASK_LOG"
```  

**任务列表**  

### 使用post-merge钩子脚本  
```
#!/bin/sh

codeDir=$(cd $(dirname $0); pwd)'/../../'

service supervisord restart     #重启队列

cd $codeDir
composer install

cd client
npm install
npm run build
```

## 开发环境规范
### 开发环境使用pre-push钩子
```
#!/bin/sh

codeDir=$(cd $(dirname $0); pwd)'/../../'
cd $codeDir
./vendor/bin/phpunit
```

## 接口列表

| URI   | Method  | Description |     
| ----  | :-----: | ----------: |
| /records | GET | 获取所有战绩信息 |
| /records | POST | 查询单个玩家战绩 |
| /record-info | POST | 根据战绩id查询单条战绩详情 |
| /players | GET | 获取所有玩家信息 |
| /players | POST | 查询玩家 | 
| /top-up | POST | 玩家充值 | 

## 接口调用规范
### 参数签名计算方法
用户提交的参数除sign外，都要参与签名。  

首先，将待签名字符串按照参数名进行排序(首先比较所有参数名的第一个字母，按abcd顺序排列，若遇到相同首字母，则看第二个字母，以此类推)。  

例如：对于如下的参数进行签名  
```
parameters={"api_key=hello-world","uid=10000"};
```   
生成待签名的字符串:   
```
api_key=hello-world&uid=10000
```  

然后，将待签名字符串尾部添加私钥参数生成最终待签名字符串。
例如:  
```
api_key=hello-world&uid=10000&secret_key=secretKey
```  
注意，"&secret_key=secretKey"为签名必传参数。  
最后，利用32位MD5算法，对最终待签名字符串进行签名运算，然后将计算结果中的字母转为大写，从而得到签名结果字符串(该字符串赋值于参数sign)  

参考如下代码：
```
protected function getSign(Array $param = null)
{
    $param['api_key'] = $this->apiKey;
    ksort($param);
    $sign = "";
    foreach ($param as $key => $value) {
        $sign .= "{$key}={$value}&";
    }
    $sign .= "secret_key={$this->secretKey}";
    return strtoupper(md5($sign));
}
```  


## 游戏端接口
> **https://down.yxx.max78.com/casino/back/htmls/agentx/**

| URI | Method | Description |
| ----  | :-----: | ----------: |
| users.php | GET | 获取玩家列表 |
| user.php| POST | 获取指定玩家信息 |
| recharge.php | POST | 给指定玩家充值 |

