<?php
/**
 * @ingroup DispletReader_Woopa
 */
interface DispletReader_Woopa_Admin_Ajax_Interface
{
	public function process_post(array $post);

	public function process_get(array $get);
}
