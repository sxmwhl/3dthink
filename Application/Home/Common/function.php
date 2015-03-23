<?php
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
function check_x3d_document($filename,$tofilename=""){
	$files=new \Think\Storage\Driver\File();
	$contents=$files->read($filename);
	if(stripos($contents,'<x3d')===false)return false;
	if(stripos($contents,'<scene')===false)return false;
	if(stripos($contents,'<shape')===false)return false;
	$tag_str="<Anchor><Appearance><Arc2D><ArcClose2D><AudioClip><Background><Billboard><BooleanFilter><BooleanSequencer><BooleanToggle><BooleanTrigger><Box><CADAssembly><CADFace><CADLayer><CADPart><Circle2D><Collision><Color><ColorInterpolator><ColorRGBA><component><ComposedCubeMapTexture><ComposedShader><ComposedTexture3D><Cone><connect><Contour2D><ContourPolyline2D><Coordinate><CoordinateDouble><CoordinateInterpolator><CoordinateInterpolator2D><Cylinder><CylinderSensor><DirectionalLight><Disk2D><ElevationGrid><EspduTransform><EXPORT><ExternProtoDeclare><Extrusion><field><fieldValue><FillProperties><FloatVertexAttribute><Fog><FogCoordinate><FontStyle><GeneratedCubeMapTexture><GeoCoordinate><GeoElevationGrid><GeoLocation><GeoLOD><GeoMetadata><GeoOrigin><GeoPositionInterpolator><GeoTouchSensor><GeoViewpoint><Group><HAnimDisplacer><HAnimHumanoid><HAnimJoint><HAnimSegment><HAnimSite><head><ImageCubeMapTexture><ImageTexture><ImageTexture3D><IMPORT><IndexedFaceSet><IndexedLineSet><IndexedQuadSet><IndexedTriangleFanSet><IndexedTriangleSet><IndexedTriangleStripSet><Inline><IntegerSequencer><IntegerTrigger><IS><KeySensor><LineProperties><LineSet><LoadSensor><LocalFog><LOD><Material><Matrix3VertexAttribute><Matrix4VertexAttribute><meta><MetadataDouble><MetadataFloat><MetadataInteger><MetadataSet><MetadataString><MovieTexture><MultiTexture><MultiTextureCoordinate><MultiTextureTransform><NavigationInfo><Normal><NormalInterpolator><NurbsCurve><NurbsCurve2D><NurbsOrientationInterpolator><NurbsPatchSurface><NurbsPositionInterpolator><NurbsSet><NurbsSurfaceInterpolator><NurbsSweptSurface><NurbsSwungSurface><NurbsTextureCoordinate><NurbsTrimmedSurface><OrientationInterpolator><PackagedShader><PixelTexture><PixelTexture3D><PlaneSensor><PointLight><PointSet><Polyline2D><Polypoint2D><PositionInterpolator><PositionInterpolator2D><ProgramShader><ProtoBody><ProtoDeclare><ProtoInstance><ProtoInterface><ProximitySensor><QuadSet><ReceiverPdu><Rectangle2D><ROUTE><ScalarInterpolator><Scene><ShaderPart><ShaderProgram><Shape><SignalPdu><Sound><Sphere><SphereSensor><SpotLight><StaticGroup><StringSensor><Switch><Text><TextureBackground><TextureCoordinate><TextureCoordinate3D><TextureCoordinate4D><TextureCoordinateGenerator><TextureTransform><TextureTransform3D><TextureTransformMatrix3D><TimeSensor><TimeTrigger><TouchSensor><Transform><TransmitterPdu><TriangleFanSet><TriangleSet><TriangleSet2D><TriangleStripSet><Viewpoint><VisibilitySensor><WorldInfo><X3D>";
	$contents=strip_tags($contents,$tag_str);
	$contents = preg_replace( "@>(.*?)<@is", ">\n<", $contents );
	$start=stripos($contents, '<x3d');
	$contents=substr($contents, $start);
	if($tofilename!=="")$files->put($tofilename,$contents);
	return true;
}