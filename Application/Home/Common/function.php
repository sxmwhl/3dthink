<?php
/** * 用DES算法加密/解密字符串 * *
@param string $string 待加密的字符串
@param string $key 密匙，和管理后台需保持一致
@return string 返回经过加密/解密的字符串
*/
// 加密，注意，加密前需要把数组转换为json格式的字符串
function des_encrypt($string, $key) {
	$size = mcrypt_get_block_size('des', 'ecb');
	$string = mb_convert_encoding($string, 'GBK', 'UTF-8');
	$pad = $size - (strlen($string) % $size);
	$string = $string . str_repeat(chr($pad), $pad);
	$td = mcrypt_module_open('des', '', 'ecb', '');
	$iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	@mcrypt_generic_init($td, $key, $iv);
	$data = mcrypt_generic($td, $string);
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	$data = base64_encode($data);
	return $data;
}

// 解密，解密后返回的是json格式的字符串
function des_decrypt($string, $key) {
	$string = base64_decode($string);
	$td = mcrypt_module_open('des', '', 'ecb', '');
	$iv = @mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
	$ks = mcrypt_enc_get_key_size($td);
	@mcrypt_generic_init($td, $key, $iv);
	$decrypted = mdecrypt_generic($td, $string);
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	$pad = ord($decrypted{strlen($decrypted) - 1});
	if($pad > strlen($decrypted)) {
		return false;
	}
	if(strspn($decrypted, chr($pad), strlen($decrypted) - $pad) != $pad) {
		return false;
	}
	$result = substr($decrypted, 0, -1 * $pad);
	$result = mb_convert_encoding($result, 'UTF-8', 'GBK');
	return $result;
}
/** *删除目录及目录下文件**
 @param string $dir 删除目录路径
 @return bool 返回是否正确删除
 */
function deldir($dir) {
	//先删除目录下的文件：
	$dh=opendir($dir);
	while ($file=readdir($dh)) {
		if($file!="." && $file!="..") {
			$fullpath=$dir."/".$file;
			if(!is_dir($fullpath)) {
				unlink($fullpath);
			} else {
				deldir($fullpath);
			}
		}
	}

	closedir($dh);
	//删除当前文件夹：
	if(rmdir($dir)) {
		return true;
	} else {
		return false;
	}
}
/** *删除目录及目录下文件**
@param string $dir 删除目录路径
@return bool 返回创建结果
*/
function create_dir($dir){
	return is_dir($dir) or (create_dir(dirname($dir)) and mkdir ($dir , 0777));
}
/** *删除目录及目录下文件**
@param string $filename 检测处理的文件名
@param string $tofilename 处理后文件存放位置
@return bool 被检测文件是否符合要求
*/
function check_x3d_document($filename,$tofilename=""){
	$files=new \Think\Storage\Driver\File();
	$contents=$files->read($filename);
	if(stripos($contents,'<x3d')===false)return false;
	if(stripos($contents,'<scene')===false)return false;
	if(stripos($contents,'<shape')===false)return false;
	$tag_str="<solidOfRevolution><Pyramid><Dish><Torus><Nozzle><RectangularTorus><depthmode><particleset><Plane><SurfaceShaderTexture><CommonSurfaceShader><Anchor><binaryGeometry><Appearance><Arc2D><ArcClose2D><AudioClip><Background><Billboard><BooleanFilter><BooleanSequencer><BooleanToggle><BooleanTrigger><Box><CADAssembly><CADFace><CADLayer><CADPart><Circle2D><Collision><Color><ColorInterpolator><ColorRGBA><component><ComposedCubeMapTexture><ComposedShader><ComposedTexture3D><Cone><connect><Contour2D><ContourPolyline2D><Coordinate><CoordinateDouble><CoordinateInterpolator><CoordinateInterpolator2D><Cylinder><CylinderSensor><DirectionalLight><Disk2D><ElevationGrid><EspduTransform><EXPORT><ExternProtoDeclare><Extrusion><field><fieldValue><FillProperties><FloatVertexAttribute><Fog><FogCoordinate><FontStyle><GeneratedCubeMapTexture><GeoCoordinate><GeoElevationGrid><GeoLocation><GeoLOD><GeoMetadata><GeoOrigin><GeoPositionInterpolator><GeoTouchSensor><GeoViewpoint><Group><HAnimDisplacer><HAnimHumanoid><HAnimJoint><HAnimSegment><HAnimSite><head><ImageCubeMapTexture><ImageTexture><ImageTexture3D><IMPORT><IndexedFaceSet><IndexedLineSet><IndexedQuadSet><IndexedTriangleFanSet><IndexedTriangleSet><IndexedTriangleStripSet><Inline><IntegerSequencer><IntegerTrigger><IS><KeySensor><LineProperties><LineSet><LoadSensor><LocalFog><LOD><Material><Matrix3VertexAttribute><Matrix4VertexAttribute><meta><MetadataDouble><MetadataFloat><MetadataInteger><MetadataSet><MetadataString><MovieTexture><MultiTexture><MultiTextureCoordinate><MultiTextureTransform><NavigationInfo><Normal><NormalInterpolator><NurbsCurve><NurbsCurve2D><NurbsOrientationInterpolator><NurbsPatchSurface><NurbsPositionInterpolator><NurbsSet><NurbsSurfaceInterpolator><NurbsSweptSurface><NurbsSwungSurface><NurbsTextureCoordinate><NurbsTrimmedSurface><OrientationInterpolator><PackagedShader><PixelTexture><PixelTexture3D><PlaneSensor><PointLight><PointSet><Polyline2D><Polypoint2D><PositionInterpolator><PositionInterpolator2D><ProgramShader><ProtoBody><ProtoDeclare><ProtoInstance><ProtoInterface><ProximitySensor><QuadSet><ReceiverPdu><Rectangle2D><ROUTE><ScalarInterpolator><Scene><ShaderPart><ShaderProgram><Shape><SignalPdu><Sound><Sphere><SphereSensor><SpotLight><StaticGroup><StringSensor><Switch><Text><TextureBackground><TextureCoordinate><TextureCoordinate3D><TextureCoordinate4D><TextureCoordinateGenerator><TextureTransform><TextureTransform3D><TextureTransformMatrix3D><TimeSensor><TimeTrigger><TouchSensor><Transform><TransmitterPdu><TriangleFanSet><TriangleSet><TriangleSet2D><TriangleStripSet><Viewpoint><VisibilitySensor><WorldInfo><X3D>";
	$contents=strip_tags($contents,$tag_str);
	$contents = preg_replace( "@>(.*?)<@is", ">\n<", $contents );
	$start=stripos($contents, '<x3d');
	$contents=substr($contents, $start);
	if($tofilename!=="")$files->put($tofilename,$contents);
	return true;
}
/**
* in查询结果排序
*@param string $order 排序字符串，如：1,2,8
*@param array $inResult IN查询结果
*@param array $key IN关键字
*@return array 排序后结果
*/
function order_in($order,$inResult,$key){
	$str = explode(',',$order);
	$arr = array();
	for($i=0; $i<count($str); $i++)  {
		for($j=0; $j<count($inResult); $j++){
			if($str[$i] == $inResult[$j][$key]){
				$arr[] = $inResult[$j];
				break;
			}
		}
	}
	return $arr;
}
/**
* 系统邮件发送函数
* @param string $to    接收邮件者邮箱
* @param string $name  接收邮件者名称
* @param string $subject 邮件主题
* @param string $body    邮件内容
* @param string $attachment 附件列表
* @return boolean
*/
function think_send_mail($to, $name, $subject = '', $body = '', $attachment = null){
	$config = C('THINK_EMAIL');
	vendor('PHPMailer.PHPMailerAutoload'); //从PHPMailer目录导class.phpmailer.php类文件
	$mail             = new PHPMailer(); //PHPMailer对象
	$mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
	$mail->IsSMTP();  // 设定使用SMTP服务
	$mail->SMTPDebug  = 0;                     // 关闭SMTP调试功能
	// 1 = errors and messages
	// 2 = messages only
	$mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
	$mail->SMTPSecure = 'ssl';                 // 使用安全协议
	$mail->Host       = $config['SMTP_HOST'];  // SMTP 服务器
	$mail->Port       = $config['SMTP_PORT'];  // SMTP服务器的端口号
	$mail->Username   = $config['SMTP_USER'];  // SMTP服务器用户名
	$mail->Password   = $config['SMTP_PASS'];  // SMTP服务器密码
	$mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
	$replyEmail       = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
	$replyName        = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
	$mail->AddReplyTo($replyEmail, $replyName);
	$mail->Subject    = $subject;
	$mail->AltBody    = "为了查看该邮件，请切换到支持 HTML 的邮件客户端";
	$mail->MsgHTML($body);
	$mail->AddAddress($to, $name);
	$mail->addBCC($config['BCC_EMAIL'],'Admin');
	if(is_array($attachment)){ // 添加附件
		foreach ($attachment as $file){
			is_file($file) && $mail->AddAttachment($file);
		}
	}
	return  $mail->Send() ? true : $mail->ErrorInfo;
}

/**
 * 二维码生成函数
 * @param string $data 要生成二维码的数据；
 * @param mixed $isfile 是否生成文件，生成则为文件路径；
 * @param string $level 默认L 'L M Q H' 从低到高四个容错级别；
 * @param int $size 生成文件大小 默认为3;
 * @return mixed QRcode::png
 */
function think_phpqrcode($data,$isfile=FALSE,$level='L',$size=3){
	vendor("phpqrcode.phpqrcode");
	return QRcode::png($data, $isfile, $level, $size);
}

/**
 * 检测是否手机浏览器 
 * @return boolean true 是手机浏览器，false不是 
 */
function is_mobile() {
	$mobile = array();
	static $mobilebrowser_list ='Mobile|iPhone|Android|WAP|NetFront|JAVA|OperasMini|UCWEB|WindowssCE|Symbian|Series|webOS|SonyEricsson|Sony|BlackBerry|Cellphone|dopod|Nokia|samsung|PalmSource|Xphone|Xda|Smartphone|PIEPlus|MEIZU|MIDP|CLDC';
	//note 获取手机浏览器
	if(preg_match("/$mobilebrowser_list/i", $_SERVER['HTTP_USER_AGENT'], $mobile)) {
		return true;
	}else{
		if(preg_match('/(mozilla|chrome|safari|opera|m3gate|winwap|openwave)/i', $_SERVER['HTTP_USER_AGENT'])) {
			return false;
		}else{
			if($_GET['mobile'] === 'yes') {
				return true;
			}else{
				return false;
			}
		}
	}
}