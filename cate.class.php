<?php
	
	include_once "mysql.class.php";
    //继承父类
	class Cate extends Connect{
		//继承父类的构造方法	
		public function __construct(){
			parent::__construct();
		}
		
		
		//添加类别
		public function insert($name, $pid, $path){
			$sql="insert into cate (cate_name, parent_id, cate_path) values ('$name', $pid, '$path')";
			return $this->setRecords($sql);
		}
		
		
		//编辑类别
		public function update($name, $id){
			$sql="update cate set cate_name='$name' where cate_id=$id";
			return $this->setRecords($sql);		
		}
		
		
		//转移类别
		public function move( $id, $parentId ){
			try{
				
				$this->zhixing("BEGIN");
				
				$sql="select cate_path from cate where cate_id=$id";
				$data=$this->getRecords($sql);
				$qian=$data[0]['cate_path'] . $id . "," ;//需要修改的路径（其子类中）
				
				if( $parentId==0 ){
					$sql="update cate set parent_id=0, cate_path=',' where cate_id=$id";//修改成转移后
					$this->setRecords($sql);
					
					$sql="select cate_path from cate where cate_id=$id";//查找转移后的路径
					$data=$this->getRecords($sql);
					$hou=$data[0]['cate_path'] . $id . ",";//要被改成的路径(其子类中)
				}else{
					$sql="select cate_path from cate where cate_id=$parentId";//查出他要转移到的类别的路径
					$data=$this->getRecords($sql);
					$p=$data[0]['cate_path'] . $parentId . ",";//他转移后的路径
					$hou=$p . $id . ",";//要被改成的路径(其子类中)
					
					$sql="update cate set cate_path='$p',parent_id=$parentId where cate_id=$id";
					$this->setRecords($sql);
				}
				
				$sql="select * from cate where cate_path like '%,$id,%'";//子类
				$rs=$this->setRecords($sql);
				while( $row=mysql_fetch_assoc($rs) ){
					$zi=$row['cate_path'];
					$new=str_replace("$qian","$hou","$zi");
					$sql="update cate set cate_path='$new' where cate_id=" . $row['cate_id'];
					$this->setRecords($sql);
				}
				$this->zhixing("COMMIT");
				$this->zhixing("END");	
				return true;
			}catch( Exception $e){
				//$e->getMessage();
				$this->zhixing("ROLLBACK");
				$this->zhixing("END");	
				return false;
			}
			
			
		}
		
		//删除类别
		public function delete($id){
			$sql="select count(*) as count from cate where parent_id=$id";
			$data=$this->getRecords($sql);
			if($data[0][count]>0){
				return false;
			}else{
				$sql="delete from cate where cate_id= $id";
				return $this->setRecords($sql);
			}
		
		
		}
		//执行sql语句
		public function zhixing($sql){
			return $this->setRecords($sql);
		}
		
		//把sql语句变成数组
		public function select($sql){
			return $this->getRecords($sql);
		}
		
		
		
		
	}
	
	
?>