<?php
 #Grande Rael!! raelcunha.com/template
 #add_string($varname, $string)
class Template {
	private $vars = array();
	private $values = array();
	private $properties = array();
	private $instances = array();
	private $blocks = array();
	private $parents = array();
	private $accurate;
	private $erros_template;
	//private $filename;
	
	private static $REG_NAME = "([[:alnum:]]|_)+";
	
		 
	
	public function __construct($filename, $string = null ,$accurate = false){
		$this->accurate = $accurate;
		//$html = @$html;
		
		if ($filename == "texto")
		$this->ler_texto($string);
		else{
		
		/* if ($GLOBALS['servidor'] == "on"){
			$arqui = file_get_contents($filename);
			if (!preg_match_all("<!-- html_limpo -->", $arqui, $r)){
			$limpo = $this->limpa_html($arqui);
			file_put_contents($filename, "<!-- html_limpo -->" . $limpo);}
			} */
			
		$this->loadfile(".", $filename);
        $GLOBALS['filename'] = $filename;
		}
	}


	public function addFile($varname, $filename){
	
		if(!$this->existe_var($varname)) throw new InvalidArgumentException("addFile: var $varname não existe");
		
		/* if ($GLOBALS['servidor'] == "on"){
			$arqui = file_get_contents($filename);
			if (!preg_match_all("<!-- html_limpo -->", $arqui, $r)){
			$limpo = $this->limpa_html($arqui);
			file_put_contents($filename, "<!-- html_limpo -->" . $limpo);}
			} */
			
		$this->loadfile($varname, $filename);
	}
	

	public function __set($varname, $value){

		if(!$this->existe_var($varname))
		$this->erros_template = "<u>*AVISO</u>: A variavel $varname não existe no arquivo: <i>". $GLOBALS['filename'] ."</i>" . "<br />" . @$this->erros_template;
		

		 $stringValue = $value;
		if(is_object($value)){
			$this->instances[$varname] = $value;
			if(!array_key_existe_var($varname, $this->properties)) $this->properties[$varname] = array();
			if(method_existe_var($value, "__toString")) $stringValue = $value->__toString();
			else $stringValue = "Object";
		} 
		 
		
		$this->setValue($varname, $stringValue);
		
		return $value;
	}
	
	

	public function __get($varname){
		if (isset($this->values["{".$varname."}"]))
			return $this->values["{".$varname."}"];
			else
			echo ("AVISO: A variavel $varname não existe!!");
	}


	public function existe_var($varname){
		return in_array($varname, $this->vars);
	}
    public function existe_bloco($blockname){
        return in_array($blockname, $this->blocks);
    }

	public function exibirBlocos(){
		
		while (list($key, $val) = each($this->vars)) { 
		
			if (@$teste == "")
				$teste ="Todos os Blocos: $val"; 
				else
				$teste = @$teste . ", " . $val  ;
			}

		$this->erros_template =  $teste;        
    }

	private function loadfile($varname, $filename) {
		if (!file_exists($filename)){
			echo "<font COLOR='#f00'><u>*AVISO</u>: O Arquivo <i>$filename</i> não existe!!</font><br />". @$this->erros_template;
			
			exit;
			}else{
			// Reading file and hiding comments
			$str = preg_replace("/<!---.*?--->/smi", "", file_get_contents($filename));
			
			$str = preg_replace("/<!---.*?--->/smi", "", file_get_contents($filename));
			
			
			$blocks = $this->recognize($str, $varname);
			if (empty($str))
				echo "<font COLOR='#f00'><u>*AVISO</u>: O Arquivo <i>$filename</i> está vazio!!</font><br />". @$this->erros_template;
			
			
			$this->setValue($varname, $str);
			
			$this->createBlocks($blocks, $filename);}
	}

#condtec 17-9-2013
public function ler_texto($texto){
		// Reading file and hiding comments
			$str = preg_replace("/<!---.*?--->/smi", "", $texto);
			
			$str = preg_replace("/<!---.*?--->/smi", "", $texto);
			
			
			$blocks = $this->recognize($str, ".");
			if (empty($str))
				echo "<font COLOR='#f00'><u>*AVISO</u>: O Texto está vazio!!</font><br />". @$this->erros_template;
			
			
			$this->setValue(".", $str);
			
			$this->createBlocks($blocks, "texto");
	}
	
#condtec 11-9-2013
public function add_string($varname, $string){
		if(!$this->existe_var($varname)) throw new InvalidArgumentException("add_string: A var $varname não existe");
		$this->abrir_string($varname, $string);
	}
#condtec 11-9-2013	
private function abrir_string($varname, $string) {
		if ($string == ""){
			echo "<font COLOR='#f00'><u>*AVISO</u>: A string esta vazia!!</font><br />". @$this->erros_template;
			exit;
			}else{
			// Reading file and hiding comments
			$str = preg_replace("/<!---.*?--->/smi", "", $string);
			
			
			$blocks = $this->recognize($str, $varname);
			if (empty($str))
				echo "<font COLOR='#f00'><u>*AVISO</u>: O Arquivo <i>$string</i> está vazio!!</font><br />". @$this->erros_template;
			
			
			$this->setValue($varname, $str);
			
			$this->createBlocks($blocks, $string);}
	}
	
	
function organiza($tudo ,$html=0, $css=0,$java=0){  
		
		
	return $this->limpa_html($tudo ) ;
	
	}
	

	function limpa_css($css){  
	$css = str_replace('; ',';',str_replace(' }','}',str_replace('{ ','{',str_replace(array("\r\n","\r","\n","\t",'  ','    ','    '),"",preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!','',$css))))); 
	return $css;
	}
	
	
function limpa_html($html){
	preg_match_all('!(&lt;(?:code|pre).*&gt;[^&lt;]+&lt;/(?:code|pre)&gt;)!',$html,$pre);#exclude pre or code tags<br />
	$html = preg_replace('!&lt;(?:code|pre).*&gt;[^&lt;]+&lt;/(?:code|pre)&gt;!', '#pre#', $html);#removing all pre or code tags<br />
	$html = preg_replace('#&lt;!--[^\[].+--&gt;#', '', $html);#removing HTML comments<br />
	$html = preg_replace('/[\r\n\t]+/', ' ', $html);#remove new lines, spaces, tabs<br />
	$html = preg_replace('/&gt;[\s]+&lt;/', '&gt;&lt;', $html);#remove new lines, spaces, tabs<br />
	$html = preg_replace('/[\s]+/', ' ', $html);#remove new lines, spaces, tabs<br />
	if(!empty($pre[0]))
		foreach($pre[0] as $tag)
	$html = preg_replace('!#pre#!', $tag, $html,1);#putting back pre|code tags<br />
	return $html;
	}
	
	
	private function recognize(&$content, $varname){
		$blocks = array();
		$queued_blocks = array();
		foreach (explode("<!--", $content) as $line ) {
		$line ="<!--".$line;
			if (strpos($line, "{")!==false) $this->identifyVars($line);
			if (strpos($line, "<!--")!==false) $this->identifyBlocks($line, $varname, $queued_blocks, $blocks);
		}
		return $blocks;
	}


	private function identifyBlocks(&$line, $varname, &$queued_blocks, &$blocks){
		$reg = "/<!--\s*INI\s+(".self::$REG_NAME.")\s*-->/sm";
		preg_match($reg, $line, $m);
		if (1==preg_match($reg, $line, $m)){
			if (0==sizeof($queued_blocks)) $parent = $varname;
			else $parent = end($queued_blocks);
			if (!isset($blocks[$parent])){
				$blocks[$parent] = array();
			}
			$blocks[$parent][] = $m[1];
			$queued_blocks[] = $m[1];
		}
		$reg = "/<!--\s*FIM\s+(".self::$REG_NAME.")\s*-->/sm";
		if (1==preg_match($reg, $line)) array_pop($queued_blocks);
	}
	

	private function identifyVars(&$line){
		$r = preg_match_all("/{(".self::$REG_NAME.")((\-\>(".self::$REG_NAME."))*)?}/", $line, $m);
		if ($r){
			for($i=0; $i<$r; $i++){
				// Object var detected
				if($m[3][$i] && (!array_key_existe_var($m[1][$i], $this->properties) || !in_array($m[3][$i], $this->properties[$m[1][$i]]))){
					$this->properties[$m[1][$i]][] = $m[3][$i];
				}
				if(!in_array($m[1][$i], $this->vars)) $this->vars[] = $m[1][$i];
			}
		}
	}
	

	private function createBlocks(&$blocks, $filename) {
		$this->parents = array_merge($this->parents, $blocks);
		foreach($blocks as $parent => $block){
			foreach($block as $chield){
				/*
				if(in_array($chield, $this->blocks)) {
				$this->erros_template = "Bloco duplicado ($chield) no arquivo: <i>". $filename ."</i><br />". @$this->erros_template;
				$this->show();
				//exit;
				}*/
				
				$this->blocks[] = $chield;
				$this->setBlock($parent, $chield,$filename);
			}
		}
	}
	

	private function setBlock($parent, $block,$filename) {
		$name = "B_".$block;
		$str = $this->getVar($parent);
		if($this->accurate){
			$str = str_replace("\r\n", "\n", $str);
			$reg = "/\t*<!--\s*INI\s+$block\s+-->\n*(\s*.*?\n?)\t*<!--\s+FIM\s+$block\s*-->\n?/sm";
		} 
		else $reg = "/<!--\s*INI\s+$block\s+-->\s*(\s*.*?\s*)<!--\s+FIM\s+$block\s*-->\s*/sm";
		if(1!==preg_match($reg, $str, $m)) {
			$this->erros_template = "<font COLOR='#f00'><u>*AVISO</u>: O Bloco $block esta mal formatado em <i>$filename</i>, tem que ser montado da seguinte maneira:</font> <br /><br />
			&lt;!-- INI $block --&gt;<br />
				&nbsp;&nbsp;&nbsp;&nbsp;{BLOCOS}<br />
			&lt;!-- FIM $block --&gt; 
			" . "<br />" . @$this->erros_template;
			//$this->show();
			//echo "Bloco <b>$block</b> está mal formado!! ";
			
			//exit;
			}
		
		$this->setValue($name, '');
		@$this->setValue($block, $m[1]);
		$this->setValue($parent, preg_replace($reg, "{".$name."}", $str));
	}


	private function setValue($varname, $value) {			
			$this->values["{".$varname."}"] = $value;
		}
	
	private function getVar($varname) {
	
		return $this->values['{'.$varname.'}'];
	
	}
	

	public function clear($varname) {
		$this->setValue($varname, "");
	}
	
	function subst($varname) {
		$s = $this->getVar($varname);
		// Common variables replacement
		$s = str_replace(array_keys($this->values), $this->values, $s);
		// Object variables replacement
		foreach($this->instances as $var => $instance){
			foreach($this->properties[$var] as $properties){
				if(false!==strpos($s, "{".$var.$properties."}")){
					$pointer = $instance;
					$property = explode("->", $properties);
					for($i = 1; $i < sizeof($property); $i++){
						$obj = str_replace('_', '', $property[$i]);
						// Non boolean accessor
						if(method_existe_var($pointer, "get$obj")){
							$pointer = $pointer->{"get$obj"}();
						}
						// Boolean accessor
						elseif(method_existe_var($pointer, "is$obj")){
							$pointer = $pointer->{"is$obj"}();
						}
						// Magic __get accessor
						elseif(method_existe_var($pointer, "__get")){
							$pointer = $pointer->__get($property[$i]);
						}
						// Accessor dot not existe_var: throw Exception
						else {
							$className = $property[$i-1] ? $property[$i-1] : get_class($instance);
							$class = is_null($pointer) ? "NULL" : get_class($pointer);
							throw new BadMethodCallException("não existe método na classe ".$class." para acessar ".$className."->".$property[$i]);
						}
					}
					// Checking if final value is an object
					if(is_object($pointer)){
						if(method_existe_var($pointer, "__toString")){
							$pointer = $pointer->__toString();
						} else {
							$pointer = "Object";
						}
					}
					// Replace
					$s = str_replace("{".$var.$properties."}", $pointer, $s);
				}
			}
		}
		return $s;
	}
	

	private function clearBlocks($block) {
		if (isset($this->parents[$block])){
			$chields = $this->parents[$block];
			foreach($chields as $chield){
				$this->clear("B_".$chield);
			}
		}
	}
	

	public function block($block, $append = true) {
		if(!in_array($block, $this->blocks))
			$this->erros_template = "<u>*AVISO</u>: O Bloco $block não existe no arquivo: <i>". $GLOBALS['filename'] ."</i>" . "<br />" . @$this->erros_template;
		else
		//("AVISO: O Bloco $block não existe!!");
		if ($append) $this->setValue("B_".$block, $this->getVar("B_".$block) . $this->subst($block));
		else 
		$this->setValue("B_".$block, $this->subst($block));
		$this->clearBlocks($block);
	}
	

	public function parse($mostrar_blocos) {
		// After subst, remove empty vars
		// echo $mostrar_blocos;
		#condtec 31-01 mostra os blocos		
		if(@$mostrar_blocos == 0)
			return preg_replace("/{(".self::$REG_NAME.")((\-\>(".self::$REG_NAME."))*)?}/", "", $this->subst("."));
			else
			return $this->subst(".");
	}


	 public function exibir($var) {
		$this->erros_template = @$var;
	}
	public function show($mostrar_blocos=0) {
		
		if ($this->existe_var("ERROS_TEMPLATE"))
		if ($this->erros_template != ""){
			$this-> ERROS_TEMPLATE = $this->erros_template;
			if ($this->existe_bloco("B_ERROS_TEMPLATE"))
				$this->block("B_ERROS_TEMPLATE");
			}else
			echo $this->erros_template;
			
			
				
				echo $this->parse($mostrar_blocos);
		
	}
}
?>
