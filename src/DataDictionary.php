<?php
/**
 * This file is part of atk code generator
 *
 */

namespace atkbuilder;


define('TA_TAG'             ,0);
define('TA_ID'              ,1);
define('TA_DESCRIPTION'     ,2);
define('TA_IDX_FIELDS'      ,2);
define('TA_MENU'            ,3);
define('TA_TYPE'            ,3);
define('TA_FLAGS'           ,4);
define('TA_SEARCH'          ,5);

define('TAAPP_ID'           ,1);

define('TAMOD_ID'           ,1);
define('TAMOD_DESCRIPTION'  ,2);
define('TAMOD_MENU'         ,3);


define('TANOD_ID'           ,1);
define('TANOD_DESCRIPTION'  ,2);
define('TANOD_ACTIONS'      ,3);
define('TANOD_FLAGS'        ,4);
define('TANOD_SEARCHABLE'   ,5);
define('TANOD_INSTALLABLE'  ,6);
define('TANOD_NODETYPE'     ,7);
define('TANOD_NOMENU'       ,8);

define('TAATR_ID'           ,0);
define('TAATR_DESCRIPTION'  ,1);
define('TAATR_TYPE'         ,2);
define('TAATR_PARAMS'       ,3);
define('TAATR_TABS'         ,4);

define('TADB_NAME'          ,1);
define('TADB_USER'          ,2);
define('TADB_PASS'          ,3);
define('TADB_HOST'          ,4);
define('TADB_PORT'          ,5);
define('TADB_CHARSET'       ,6);
//define('TA_SEARCH'        ,7);



class DataDictionary
{
    /**
     * Array containing the data dictionary
     * @access private
     * @var array
     */
    private $dd = array();

    /**
     * Array containing configuration values
     * @access private
     * @var array
     */
    private $config;

    /**
     * Analizes the DefFile and builds a Data Dictionary
     *
     * @param string $def_file The path of the DefFile
     * @param array $config An array of configuration values.
     */
    function __construct(string $def_file, Config $config)
    {
		$this->config = $config;
        $this->config->syslog->log("Reading definition file:".$def_file);
        $this->loadDefFile($def_file);
        $this->dd['lnglst']=array("es", "en");
        $this->dd['dirnme'] = dirname($def_file);
    }

    /**
     * Dumps de Data Dictionary array for inspection
     */
    public function DumpDictionary()
    {
        var_dump($this->dd);
    }

    /**
     *  Retrieves the multi dimensional array that contains the Data Dictionary
     *  @returns array A Data Dictionary
     */

    public function GetDataDictionary()
    {
        return $this->dd;
    }

   /**
    * Load de definition file, throw away comments and call the
    * proper method as identified by the first "tag"
    *
    * @param string $file the file definition path
    * @return bool true if ok
    */
    private function loadDefFile(string $file): bool
	{
		$lines = $this->get_file_lines($file);

		//Search and Replace Includes
		$expanded_lines=[];
		foreach ($lines as $line)
		{
 			if( (substr(trim($line),0,1) != '#'   ) and
                (substr(trim($line),0,2) != '////') and
                (trim($line)!='')
               )
            {
				$tags = explode(':',$line);
				if (trim($tags[0]) == 'include')
				{
					$file_name = $tags[1];
        			$this->config->syslog->log("Including definition file:".$file_name);
					$new_lines = $this->get_file_lines($file_name);
					$expanded_lines = array_merge($expanded_lines, $new_lines);
				}
				else
				{
					$expanded_lines[]=$line;
				}
            }
		}
		//Analize final expanded lines array
        foreach ($expanded_lines as $line)
        {
            if( (substr(trim($line),0,1) != '#'   ) and
                (substr(trim($line),0,2) != '////') and
                (trim($line)!='')
               )
            {
                $tags = explode(':',$line);
                foreach($tags as $key => $value)
                {
                        $tags[$key]=trim($value);
                }
                $this->checkContext($tags);
            }
		}
	return true;
    }

    /**
     * Reads the definitions file and return it's lineas as an array.
     *
     * @param string $file The Definition file
     * @return array Array of lines
     */
    private function get_file_lines(string $file): array
    {
        if (!file_exists($file))
        {
            $this->config->syslog->abort("Could'nt open file:".$file);
        }
        $file_contents=file_get_contents($file);
        $lines=explode("\n", $file_contents);
        return $lines;
    }

   /**
    * Check context and fills data dictionary entry
    *
    * @param array $tags An array of tags
    * @return bool true if ok
    */
    private function checkContext($tags)
    {
        if(!isset($tags[TA_TAG]))
        {
            return;
        }
        error_reporting(E_ALL ^ E_NOTICE);
        $tag=trim($tags[TA_TAG]);
        $this->config->syslog->debug("CurMod:".$this->cur_mod." CurNod:".$this->cur_nod." Tag:".$tag,5);
        switch ($tag)
        {
            case 'appnme':
                $this->dd['appnme']=$tags[TAAPP_ID];
                break;
            case 'lnglst':
                break;
            case 'db':
                $this->dd['db']['dbname']=$tags[TADB_NAME];
                $this->dd['db']['user']=$tags[TADB_USER];
                $this->dd['db']['password']=$tags[TADB_PASS];
                $this->dd['db']['host']=$tags[TADB_HOST] == '' ? 'localhost': $tags[TADB_HOST];
                $this->dd['db']['port']=$tags[TADB_PORT] == '' ? '3306': $tags[TADB_PORT];;
                $this->dd['db']['charset']=$tags[TADB_CHARSET] == '' ? 'utf-8' :$tags[TADB_CHARSET];
                break;
            case 'module':
                $this->dd['modules'][$tags[TAMOD_ID]]=array();
                $this->dd['modules'][$tags[TAMOD_ID]]['description']=trim($tags[TAMOD_DESCRIPTION]==''?$tags[TAMOD_ID]:$tags[TAMOD_DESCRIPTION]);
                $this->dd['modules'][$tags[TAMOD_ID]]['menu']=$tags[TAMOD_MENU] ==''?'true':'false';
                //To ease the generation of module digital signature all attributes are acummulated here in the attribute branch
                $this->dd['modules'][$tags[TAMOD_ID]]['attrs']=array();
                //To ease the generation of language files all labels are accumulated here by the attribute processing brach
                $this->dd['modules'][$tags[TAMOD_ID]]['languages']=array();
                $this->dd['modules'][$tags[TAMOD_ID]]['nodes']=array();

                $this->cur_mod=$tags[TAMOD_ID];
                break;
            case 'node':
                $this->cur_nod=$tags[TANOD_ID];
                $this->dd['modules'][$this->cur_mod]['nodes'][$this->cur_nod]=array();
                $this->dd['modules'][$this->cur_mod]['nodes'][$this->cur_nod]['id']=$this->cur_nod;
                $this->dd['modules'][$this->cur_mod]['nodes'][$this->cur_nod]['description']=trim($tags[TANOD_DESCRIPTION]==''?$tags[TA_ID]:$tags[TANOD_DESCRIPTION]);
                $this->dd['modules'][$this->cur_mod]['nodes'][$this->cur_nod]['flags']=trim($tags[TANOD_FLAGS]=='' ?'NF_ADD_LINK':$tags[TANOD_FLAGS]);
                $this->dd['modules'][$this->cur_mod]['nodes'][$this->cur_nod]['type']=trim($tags[TANOD_NODETYPE])==''? 'Node':trim($tags[TANOD_NODETYPE]);
                $this->dd['modules'][$this->cur_mod]['nodes'][$this->cur_nod]['search']=(($tags[TANOD_SEARCHABLE] == '') || (strtolower($tags[TANOD_SEARCHABLE]) == 'false')) ? false : true;
                $this->dd['modules'][$this->cur_mod]['nodes'][$this->cur_nod]['install']=$tags[TANOD_INSTALLABLE] == '' ? true : false;
                $this->dd['modules'][$this->cur_mod]['nodes'][$this->cur_nod]['actions']=trim($tags[TANOD_ACTIONS])=='' ? array('admin', 'add', 'edit', 'delete', 'view'): explode(",", $tags[TANOD_ACTIONS]);
                $this->dd['modules'][$this->cur_mod]['nodes'][$this->cur_nod]['nomenu']=trim($tags[TANOD_NOMENU])=='' ? false: true;
                $this->dd['modules'][$this->cur_mod]['nodes'][$this->cur_nod]['module']=$this->cur_mod;
                $this->dd['modules'][$this->cur_mod]['nodes'][$this->cur_nod]['menu_action']=$this->cur_mod;
                $this->dd['nodes'][$this->cur_nod][$this->cur_mod]=true;
                break;
            case 'index':
                $this->dd['modules'][$this->cur_mod]['nodes'][$this->cur_nod]['index'][$tags[TA_ID]]=$tags[TA_IDX_FIELDS];
                break;
            case 'roles':
                $this->dd['roles'][$tags[TA_ID]]['name']=$tags[TA_ID];
                $this->dd['roles'][$tags[TA_ID]]['description']=$tags[TA_DESCRIPTION];
                break;
            default: //atributos
                $this->config->syslog->debug("CurMod:".$this->cur_mod." CurNod:".$this->cur_nod." Attribute =>:".$tag,5);
                if ($tags[TAATR_ID] == '\node')
                {
                    $tags[TAATR_ID] = 'node';
                }

                if ( ($this->cur_mod == NULL) or ($this->cur_nod == NULL))
                {
                    $this->config->syslog->abort("attribute needs to be in a node inside a module:".$tags[TAATR_ID]);
                }
                $type= $tags[TAATR_TYPE];
                $params = $tags[TAATR_PARAMS];
                if ($type == "")
                {
                    $suggested_type =$this->sugestType($tags[TAATR_ID]);
                    $type=$suggested_type['type'];
                    if ($suggested_type['params'] !="")
                    {
                        if ($params!="") $params = $params." | ";
                        $params= $params.$suggested_type['params'];
                    }
                }
                $tags[TAATR_DESCRIPTION]=trim($tags[TAATR_DESCRIPTION]);
                $this->dd['modules'][$this->cur_mod]['nodes'][$this->cur_nod]['attributes'][$tags[TAATR_ID]]['description']=$tags[TAATR_DESCRIPTION];
                //To ease the language files generation every label is accumulated at the module level
                $this->dd['modules'][$this->cur_mod]['languages'][]="'".$tags[TA_TAG]."' =>'".$this->dd['modules'][$this->cur_mod]['nodes'][$this->cur_nod]['attributes'][$tags[TA_TAG]]['description']."', ";
                $this->dd['modules'][$this->cur_mod]['nodes'][$this->cur_nod]['attributes'][$tags[TAATR_ID]]['type']=$type;
                $this->dd['modules'][$this->cur_mod]['nodes'][$this->cur_nod]['attributes'][$tags[TAATR_ID]]['params']=$params;
                $this->dd['modules'][$this->cur_mod]['nodes'][$this->cur_nod]['attributes'][$tags[TAATR_ID]]['tabs']=trim( $tags[TAATR_TABS]) =='' ? 'NULL':"'".trim($tags[TAATR_TABS]."'");
                //To ease the generation of module digital signature all attributes are acummulated
                array_push($this->dd['modules'][$this->cur_mod]['attrs'],$this->dd['modules'][$this->cur_mod]['nodes'][$this->cur_nod]['attributes']);
                break;

        }
    }

    public function sugestType($field_name)
    {
        if (strstr($field_name,"hasmany_"))
        {
            list($filler, $normalized) = explode("hasmany_", $field_name);
            list($module,$node_id) = explode("__",$normalized);
            $key=$this->cur_mod."__".$this->cur_nod."_id";
            return array("type"=>"OneToManyRelation", "params"=>"AF_HIDE_LIST,'".ucfirst($module).".".ucfirst($node_id)."','".$key."'");
        }

        list($module,$node_id) = explode("__",$field_name);
        if (($module !="") and ($node_id!=""))
        {
            list($node,$id) = explode("_id", $node_id);
            return array("type"=>"ManyToOneRelation",
                            "params"=>"AF_RELATION_AUTOCOMPLETE|AF_RELATION_AUTOLINK|AF_HIDE_LIST, '".ucfirst($module).".".ucfirst($node)."'",
                            'dbtype'=>'bigint');
        }
        $fdict=$this->getFieldDictionary();
        foreach ($fdict as $entry)
        {
            foreach ($entry['words'] as $word)
            {
                //print($field_name."-".$word."<br>");
                if (strstr($field_name,$word)!==false)
                {
                    return array('type'=>$entry['type'], 'params'=> $entry['params'], 'dbtype'=>$entry['dbtype']);
                }
            }
        }
        $result=array("type" => "Attribute", "params"=>"AF_HIDE_LIST", "dbtype"=>"VARCHAR(100)");
        return $result;
    }


    private function getFieldDictionary()
    {
        return array(
                array(
                        "words"=>array(
                                        "name",
                                        "nombre",
                                        "descripcion",
                                        "description"
                            ),
                            "type" =>"Attribute",
                            "params" =>"AF_OBLIGATORY|AF_SEARCHABLE, 50",
                            "dbtype" =>"VARCHAR(50)"
                            ),
                array(
                        "words"=>array(
                                        "date",
                                        "fecha",
                                    ),
                        "type" =>"DateAttribute",
                        "params" =>"AF_DATE_STRING|AF_HIDE_LIST, 'd/m/Y', 'd/m/Y', NULL, NULL",
                        "dbtype" =>"DATE"
                ),
                array(
                        "words"=>array(
                                        "notes",
                                        "notas",
                                        "observation",
                                        "observacion",
                                        "observaciones"
                        ),
                        "type" =>"TextAttribute",
                        "params" =>"AF_HIDE_LIST",
                        "dbtype" =>"TEXT"
                ),
                array(
                        "words"=>array(
                                        "quantity",
                                        "cantidad",
                                        "count",
                                        "cuenta",
                                        "number",
                                        "numero"
                        ),
                        "type" =>"NumberAttribute",
                        "params" =>"AF_HIDE_LIST",
                        "dbtype" =>"BIGINT"
                ),
                array(
                        "words"=>array(
                                        "importe",
                                        "precio",
                                        "monto",
                                        "debe",
                                        "haber",
                                        "saldo",
                                        "ammount",
                                        "price",
                                        "total"
                        ),
                        "type" =>"CurrencyAttribute",
                        "params" =>"AF_HIDE_LIST",
                        "dbtype" =>"DEC(15, 2)"
                ),
                array(
                        "words"=>array(
                                        "hour",
                                        "hora",
                                        "time",
                                        "tiempo"
                        ),
                        "type" =>"TimeAttribute",
                        "params" =>"AF_HIDE_LIST",
                        "dbtype" =>"TIME"
                ),
                array(
                        "words"=>array(
                                        "is_",
                                        "has_",
                                        "es_",
                                        "posee_",
                                        "puede_",
                                        "active",
                                        "?"
                        ),
                        "type" =>"BoolAttribute",
                        "params" =>"AF_HIDE_LIST",
                        "dbtype" =>"INT(1)"
                ),
        );
    }
}
?>
