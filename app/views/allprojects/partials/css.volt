<style type="text/css">
	#buttonCrudGroupMenu {
		padding: 4px 4px 0px 4px;
	}

	.iconCrud {
		font-size:18px;
	}

    #formDaftar a{
        color: black;
     } /* ???? */   
    thead{
        background-color: #F7CA18;
    }
    .thumb
    {
        width: 200px;
        height: auto;
        margin: 0.2em -0.7em 0 0;
    }
    .remove_img_preview
    {
        position:relative;
        top:-50% !important;
        margin-top: -50% !important;
        right:3px;
        background:black;
        color:white;
        border-radius:50px;
        font-size:1em;
        padding: 0 0.5em 0;
        text-align:center;
        cursor:pointer;  
    }
    .remove_img_preview:before
    {
        content: "x";
    }  

   /* .ui-datatable table {
        table-layout: auto
    }*/

    /*.ui-datatable table{
      margin: 0 auto;
      width: 100%;
      clear: both;
      border-collapse: collapse;
      table-layout: fixed; 
      word-wrap:break-word;
    }*/
    .ui-datatable table{
      margin: 0 auto;
      width: 100%;
      /*border-collapse: collapse;
      table-layout: fixed; 
      word-wrap:break-word;
      clear: both;
      border-collapse: collapse;
      table-layout: fixed; 
      word-wrap:break-word;*/
    }

    th,
    td {
      white-space: break-word;/*nowrap;*/
    }
    /*div.dataTables_wrapper {
      width: 400px;
      margin: 0 auto;
    }    */


    @media screen {
      #printSection {
          display: none;
      }
    }

    @media print {
      body * {
        visibility:hidden;
      }
      #printSection, #printSection * {
        visibility:visible;
      }
      #printSection {
        position:absolute;
        left:0;
        top:0;
      }
    }

</style>