<?php
	/*
		Classes for documentation page and
		displaying such pages.
		Designed for a hierarchy of pages
	*/
	
	class DocPage {
		public $title;
		public $body;
		//Pages below this in the hierarchy
		public $subpages;
		
		function __construct($title=null,$body=null,$subpages=[]){
			$this->title=$title;
			$this->body=$body;
			$this->subpages=$subpages;
		}
		
		//Add subpage to list of subpages
		public function newSubpage($subpage){
			array_push($this->subpages,$subpage);
		}
		
		//Look for subpage by title
		public function getSubPage($spname){
			foreach($this->subpages as $i)
				if ($i->title==$spname)return $i;
			return null;
		  }
	}
	
	class Documentation {
		private $home;
		private $url;
		private $current_page;
		private $next_page;
		private $prev_page;
		private $current_page_number;
		private $next_page_number;
		private $prev_page_number;
		
		function __construct($home,$number="0",$url="?page="){
			$this->url=$url;
			$this->home=$home;
			$this->current_page_number=$number;
			$this->current_page=$this->home;
			foreach(str_split($number) as $i){
				//Find next page
				if(array_key_exists(ord($i)-ord("0")+1,$this->current_page->subpages)){
					$this->next_page=$this->current_page->subpages[ord($i)-ord("0")+1];
					}
				
				//Find previous page
				$this->prev_page=$this->current_page;
				if(array_key_exists(ord($i)-ord("0")-1,$this->current_page->subpages)){
					$this->prev_page=$this->current_page->subpages[ord($i)-ord("0")-1];
				}else{
					$this->prev_page=$this->current_page;
				}
				
				//Find current page
				$this->current_page=$this->current_page->subpages[ord($i)-ord("0")];
			}
		}
		private function rListPages($page=null,$number=null){
			if($page==null)$page=$this->home;
			if($page==null)$page=$this->current_page_number;
			$output="<ul>";
			for($i=0;$i<count($page->subpages);$i++){
				//Provide link to page in iteration
				$output.=("<li>");
				
				//Make bold if current page, else link:
				if($this->current_page->title==$page->subpages[$i]->title){
					$output.=("<strong class='blue'>");
				}else{
					//Make link
					$output.=("<a href='".$this->url.$number.$i."'>");
					
					//Get next page number
					if($this->next_page!=null && $page->subpages[$i]->title==$this->next_page->title)
						$this->next_page_number=$number.$i;
					
					//Get previous page number
					if($this->prev_page!=null && $page->subpages[$i]->title==$this->prev_page->title)
						$this->prev_page_number=$number.$i;
				}
				
				//Show page in iteration
				$output.=($page->subpages[$i]->title);
				
				//end tags
				$output.=(($this->current_page->title==$page->subpages[$i]->title)?"</strong>":"</a>");
				
				//List sub pages of that page
				$output.=$this->rListPages($page->subpages[$i],$number.$i);
					
				$output.=("</li>\n");
			}
			$output.=("</ul>\n");
			return $output;
		}
		
		public function listPages($show=true){
			$menu=$this->rListPages();
			if($show)echo($menu);
			if(count($this->current_page->subpages))
				$this->next_page_number=$this->current_page_number."0";
		}
		
		public function displayPage(){
			//Highlight.js script
			echo("<script>hljs.initHighlightingOnLoad();</script>");
			
			//Write current page
			echo("<div class='doc'>");
			echo("<h1>".$this->current_page->title."</h1>\n");
			echo($this->current_page->body);
			echo("</div>");
		
			//Manage Navigation
			if($this->next_page==null)$this->next_page_number=0;
			if($this->prev_page==$this->home)$this->prev_page_number=0;
			echo("<script>
					//Read arrow keys
					document.onkeydown=function(e){
						switch(e=e||window.event,e.keyCode){
							case 37:
								leftArrowPressed();
								break;
							case 39:
								rightArrowPressed();
						}
					};
					function rightArrowPressed(){
						window.location.href='".$this->url.$this->next_page_number."';
					}
					function leftArrowPressed(){
						window.location.href='".$this->url.$this->prev_page_number."';
					}
				</script>
			");
		}		
	}

?>