<?php
	
	include_once "mysql.class.php";
    //�̳и���
	class Cate extends Connect{
		//�̳и���Ĺ��췽��	
		public function __construct(){
			parent::__construct();
		}
		
		
		//������
		public function insert($name, $pid, $path){
			$sql="insert into cate (cate_name, parent_id, cate_path) values ('$name', $pid, '$path')";
			return $this->setRecords($sql);
		}
		
		
		//�༭���
		public function update($name, $id){
			$sql="update cate set cate_name='$name' where cate_id=$id";
			return $this->setRecords($sql);		
		}
		
		
		//ת�����
		public function move( $id, $parentId ){
			try{
				
				$this->zhixing("BEGIN");
				
				$sql="select cate_path from cate where cate_id=$id";
				$data=$this->getRecords($sql);
				$qian=$data[0]['cate_path'] . $id . "," ;//��Ҫ�޸ĵ�·�����������У�
				
				if( $parentId==0 ){
					$sql="update cate set parent_id=0, cate_path=',' where cate_id=$id";//�޸ĳ�ת�ƺ�
					$this->setRecords($sql);
					
					$sql="select cate_path from cate where cate_id=$id";//����ת�ƺ��·��
					$data=$this->getRecords($sql);
					$hou=$data[0]['cate_path'] . $id . ",";//Ҫ���ĳɵ�·��(��������)
				}else{
					$sql="select cate_path from cate where cate_id=$parentId";//�����Ҫת�Ƶ�������·��
					$data=$this->getRecords($sql);
					$p=$data[0]['cate_path'] . $parentId . ",";//��ת�ƺ��·��
					$hou=$p . $id . ",";//Ҫ���ĳɵ�·��(��������)
					
					$sql="update cate set cate_path='$p',parent_id=$parentId where cate_id=$id";
					$this->setRecords($sql);
				}
				
				$sql="select * from cate where cate_path like '%,$id,%'";//����
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
		
		//ɾ�����
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
		//ִ��sql���
		public function zhixing($sql){
			return $this->setRecords($sql);
		}
		
		//��sql���������
		public function select($sql){
			return $this->getRecords($sql);
		}
		
		
		
		
	}
	
	
?>