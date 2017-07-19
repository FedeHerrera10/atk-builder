<?php


namespace App\Modules;

use Sintattica\Atk\Core\Node;
use Sintattica\Atk\Core\Tools;
use Sintattica\Atk\Utils\atkMessageQueue;
use Sintattica\Atk\Session\SessionManager;
use Sintattica\Atk\Attributes\Attribute as A;

class AtkBuilderNode extends Node
{
	function adminHeader()
	{
		$script='<script type="text/javascript" src="./atk/javascript/newwindow.js"></script> ';
		$filter_bar=$this->getAdminFilterBar();
		$view_bar=$this->getAdminViewBar();
		$script.="<br><table width=100%><tr><td width=50%>$filter_bar</td><td align=right>$view_bar</td></tr></table><br>";
		return $script;
	}

	function adminFooter()
  	{
    	return '';
	}

	private function getAdminFilterBar()
	{
		if ( (!isset($this->admin_filters)) || (!is_array($this->admin_filters)))
		return "";
		$max_filters = count($this->admin_filters) -1;
		$a = $this->getAdminFilter();
		@$cur_filter = $a['cur_filter'];
		$prev_filter = ($cur_view - 1 ) < 0 ? $max_filters : $cur_filter - 1;
		$next_filter = ($cur_view + 1 ) > $max_filters ? 0 : $cur_filter + 1;
		$bar=Tools::href(Tools::dispatch_url($this->atkNodeUri(),'admin', array('filter_nbr' => $prev_filter)),"<<",SessionManager::SESSION_DEFAULT,false,"class='btn btn-default'")." ";
		for($i=0;$i <= $max_filters ;$i++)
		{
			$style='btn btn-default';
			if ($i == $cur_filter)
				$style='btn btn-primary';
			$a = Tools::href(Tools::dispatch_url($this->atkNodeUri(),'admin', array('filter_nbr' => $i)),$this->admin_filters[$i][0],SessionManager::SESSION_DEFAULT,false,"class='$style'")." ";
			$bar.=$a;
		}
		$bar  .= Tools::href(Tools::dispatch_url($this->atkNodeUri(),'admin', array('filter_nbr' => $next_filter)),">>",SessionManager::SESSION_DEFAULT,false,"class='btn btn-default'");
		return $bar;
	}

	private function getAdminViewBar()
	{
		if ( (!isset($this->admin_views)) || (!is_array($this->admin_views)))
			return "";
		$max_views = count($this->admin_views) -1;
		$cur_view = $this->getAdminView();
		$prev_view = ($cur_view - 1 ) < 0 ? $max_views : $cur_view - 1;
		$next_view = ($cur_view + 1 ) > $max_views ? 0 : $cur_view + 1;
		$bar=Tools::href(Tools::dispatch_url($this->atkNodeUri(),'admin', array('view_nbr' => $prev_view)),"<<",SessionManager::SESSION_DEFAULT,false,"class='btn btn-default'")." ";
		for($i=0;$i <= $max_views ;$i++)
		{
			$a = Tools::href(Tools::dispatch_url($this->atkNodeUri(),'admin', array('view_nbr' => $i)),"Vista $i",SessionManager::SESSION_DEFAULT,false,"class='btn btn-default'")." ";
			$style="btn btn-default";
			if ($i == $cur_view)
				$style="btn btn-primary";
			$a = Tools::href(Tools::dispatch_url($this->atkNodeUri(),'admin', array('view_nbr' => $i)),"Vista $i",SessionManager::SESSION_DEFAULT,false,"class='$style'")." ";
			$bar.=$a;
		}
		$bar  .= Tools::href(Tools::dispatch_url($this->atkNodeUri(),'admin', array('view_nbr' => $next_view)),">>", SessionManager::SESSION_DEFAULT,false,"class='btn btn-default'");
		return $bar;
	}

	private function getAdminView()
	{
		$sessionManager=  SessionManager::getInstance();
		$cur_view = $sessionManager->stackVar('view_nbr');
		if ($cur_view == NULL)
			$cur_view = 0;
		return $cur_view;
	}

	private function getAdminFilter()
	{
		$sessionManager = SessionManager::getInstance();
		$cur_filter = $sessionManager->stackVar('filter_nbr');
		if ($cur_filter == NULL)
		$cur_filter = 0;
		return $cur_filter;
	}

	private function setAdminView()
	{
		if ( (!isset($this->admin_views)) || (!is_array($this->admin_views)))
		{
			return;
		}
		$cur_view = $this->getAdminView();
		$attributes = $this->getAttributeNames();
		foreach ($attributes as $name)
		{
			$this->getAttribute($name)->addFlag(A::AF_HIDE_LIST|A::AF_FORCE_LOAD);
		}
		foreach ($this->admin_views[$cur_view] as $name)
		{
			$this->getAttribute($name)->removeFlag(A::AF_HIDE_LIST);
		}
	}

	private function setAdminFilter()
	{
		if ( (!isset($this->admin_filters)) || (!is_array($this->admin_filters)))
		{
			return;
		}
		$cur_filter = $this->getAdminFilter();
		$this->addFilter($this->admin_filters[$cur_filter][1]);
	}

	function action_admin(&$handler, $record=null)
	{
		$this->setAdminView();
		$this->setAdminFilter();
		return $handler->action_admin($record);
	}

	/**
	* Chain: Recovers one and just one record from a node. if expresion
	*        is absent it yields the first record of the node.
	*        @param String $node node name in full format "module.node"
	*        @param String $where where expresion bear in mind fully qualify the field names
	*               to avoid potential problems i.e. security_user.login instead of
	*               just login.
	*         @return Record array or false if empty result set
	*/
	public function dbChain($node,$where)
	{
		if (strpos($node,'.') === false)
		{
			$node=$node.'.'.$node;
		}
		if(is_numeric($where))
		{
			$where =str_replace('.','_',$node).'.id='.$where;
		}
		$o_node = Atk::getInstance()-> atkGetNode($node);
		if(!is_object($o_node))
		{
			throw new Exception('Not a node:'.$node);
		}
		$record = $o_node->selectDb($where);
		if (count($record) <= 0)
		{
			return false;
		}
		$record[0]['node']=$node;
		return $record[0];
	}
	/**
	 * dbWrite: Adds a record to a node table.
	 * @param: node name in full format "module.node"
	 * @param: record array
	 */
	function public dbWrite($node,$record,$triggers=false)
	{
		if (strpos($node,'.') === false)
		{
			$node=$node.'.'.$node;
		}
		$db = $this->getDb();
		$node = Atk::getInstance()-> atkGetNode($node);
	  	$record['id'] = $db->nextid($node->getTable());
	  	$db->getRow('COMMIT');
	    $node->addDb($record,$triggers);
	 	$db->getRow('COMMIT');
	    return $record['id'];
	}
	/**
	 * dbReadE: Recovers a set of record.
	 *          @param string $node node name in full format
	 *          @param string $where Where expresion bear in mind fully qualify the field names
	 *                 to avoid potential problems i.e. security_user.login instead of
	 *                 just login.
	 *          @return: Record array or false if empty result set
	 */
	function public dbReadE($node,$where)
	{
		if (strpos($node,'.') === false)
		{
			$node=$node.'.'.$node;
		}
		if(is_numeric($where))
		{
			$where =str_replace('.','_',$node).'.id='.$where;
		}
		$o_node = Atk::getInstance()->atkGetNode($node);
		if(!is_object($o_node))
		{
			throw new Exception('Not a node:'.$node);
		}
		$records = a$o_node->selectDb($where);
		return $records;
	}

	/**
	 *   dbUpdate: Updates a record, it uses the atkprimkey member of the
	 *             record array to update the table. if this member is not
	 *             present an error is thrown.
	 *             It also uses the node member of the record array, if present
	 *             in order to determine wich node is updating, the node can also
	 *             be specified in the cpDbUpdate call, wich is usefull to update
	 *             a different node that has the same structure of the record's node
	 *             @param Array $record An array containing a record
	 *             @param String node specification
	 *             @return nothing
	 */
	function dbUpdate($record,$node=null)
	{
		$inc=array();
		$has_primarykey=false;
		$has_node=false;
		$record_nodename='';
		foreach($record as $key => $value)
		{
			if ($key == 'atkprimkey')
			{
				$has_primarykey= true;
			}
	        elseif ($key == 'node')
	        {
	            $has_node=true;
				$record_nodename=$value;
		    }
		    else
		    {
				array_push($inc,$key);
			}
	    }
	    if (!$has_primarykey)
	    {
			throw new Exception('No primary key for update');
		}
	    if((!$has_node) and ($node == NULL))
	    {
			throw new Exception('No  node to update');
		}
		if ($node == null)
		{
			$node=$record_nodename;
		}
		$node = Atk::getInstance()->atkGetNode($node);
	    $node->updateDb($record, false,NULL, $inc);
	}
  	/**
	 *   Display a fadding message in the header section of the node
	 *   @param string $message The message to show
	 *   @param string $background_color Background color
	 *   @param string $text_color Text color
	 */
	public function printMessage($message, $background_color='#950000', $text_color='white',$duration='6.0')
	{
		$res["helplabel"] = atktext("help");
		$msg_id="msg_".microtime();

		atkMessageQueue::addMessage("<div id='".$msg_id."' style='background-color: ".$background_color.";'><b><font color='".$text_color."'>".$message."</font></b></div><script>Effect.Fade('".$msg_id."', { duration: ".$duration." });</script> "); //     FFAB35
	}
  	/**
	 *   Renders the result set of a query passed as string into a basic HTML table 
	 *   @param string $query  The sql sentence to render
	 */

  	public function queryToHtmlTable($query)
	{
		$db = $this->getDb();
		$rows = $db->getRows($query);
		$content = '<table>';
		$content.= '<tr>';
		foreach(array_keys($rows[0]) as $column)
	    {
			$column = str_replace('_',' ', $column);
			$column =  ucwords($column);
			$content.= '<td><b><u>'.$column.'</u></b></td>';
		}
    	$content.= '</tr>';
		foreach($rows as $row)
		{
			$content.= '<tr>';
			foreach($row as $key=>$value)
			{
				if (is_numeric($value))
				{
					$content.= '<td align=right>'.$value.'</td>';
				}
				else
				{
				    $content.= '<td align=left>'.$value.'</td>';
				}
		    }
		    $content.= '</tr>';
    	}
	   	$content.='</table>';
		return $content;
	}
 
	/**
	 * Renders content in a page
	 * @param string content Content to render
	 * @param string title   Title for the content
	 */
	public function renderBox($content, $title='')
	{
		$ui = $this->getUi();
		#$output= Tools::href(Tools::dispatch_url("Security.Users", "admin"),"<span class='glyphicon glyphicon-print'></span> ");
		$box =  $ui->renderBox(array(
            'title' => $title,
            'content' => $content,
        ));
	 	$page = $this->getPage();
        $page->addContent($box);
	}
	/**Url action starting id
	/*@param string node
	/*@param string action
	/*@param $record
	*/
	
	public function urlToAction($node,$action,$record)
	{
	  $url = Tools::dispatch_url("Escuela.Alumnos","pdf", ["id" =>"{$record["id"]}", 
	  "atkselector" => $this->getTable().".id = {$record["id"]}"]);
	    return $url;
	}
}
?>
