PHP 7.1 + MySql 5.6 + Yaf
* php接口 [测试](http://api.lushuhao.club/)

可以按照以下步骤来部署和运行程序:
<p>1.请确保机器已经安装了Yaf框架, 并且已经加载入PHP;</p>
<p>2.把目录Copy到Webserver的Root目录下;</p>
<p>3.需要在php.ini里面启用如下配置，生产的代码才能正确运行：
	yaf.environ="product"</p>
<p>4.重启Webserver;</p>
<p>5.访问网址,出现Hellow Word!, 表示运行成功,否则请查看php错误日志;</p>