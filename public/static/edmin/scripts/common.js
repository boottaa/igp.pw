$(document).ready(function () {
    if($('.datatable-1').length>0){
        $('.datatable-1').dataTable();
        $('.dataTables_paginate').addClass('btn-group datatable-pagination');
        $('.dataTables_paginate > a').wrapInner('<span />');
        $('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
        $('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');

        $( '.slider-range').slider({
			    range: true,
			    min: 0,
			    max: 20000,
			    values: [ 3000, 12000 ],
			    slide: function(event, ui) {
				    $(this).find('.ui-slider-handle').attr('title', ui.value);
			    },
	    });

        $( '#amount' ).val( '$' + $( '.slider-range' ).slider( 'values', 0 ) + ' - $' + $( '.slider-range' ).slider( 'values', 1 ) );


    //Graph/Chart index.html

        $.get( "/admin/activity", function( data ) {
            var all_data = [
                { data: $.parseJSON(data)}
            ];
            // преобразуем даты в UTC
            for(var j = 0; j < all_data.length; ++j) {
                for(var i = 0; i < all_data[j].data.length; ++i)
                    all_data[j].data[i][0] = Date.parse(all_data[j].data[i][0]);
            }

            getGraph(all_data);
        });
        function getGraph($data){
            $.plot("#placeholder2", $data, {
                lines: {
                    show: true,
                    fill: true, /*SWITCHED*/
                    lineWidth: 1
                },
                points: {
                    show: true,
                    lineWidth: 5
                },
                xaxis: {
                    mode: "time",
                    timeformat: "%d.%m.%y",
                    tickLength: 0
                },
                grid: {
                    clickable: false,
                    hoverable: true,
                    autoHighlight: true,
                    mouseActiveRadius: 10,
                    aboveData: true,
                    backgroundColor: '#fff',
                    borderWidth: 0,
                    minBorderMargin: 25
                },
                colors: ['#55f3c0', '#0db37e', '#b4fae3', '#e0d1cb'],
                shadowSize: 0
            });
        }

        // Add the Flot version string to the footer

        $("#footer").prepend("Flot " + $.plot.version + " &ndash; ");

		function showTooltip(x, y, contents) {
			$('<div id="gridtip">' + contents + ' </div>').css( {
				position: 'absolute',
				display: 'none',
				top: y + 5,
				left: x + 5
			}).appendTo('body').fadeIn(300);
		}

		var previousPoint = null;
		$('#placeholder2').bind('plothover', function (event, pos, item) {
			$('#x').text(pos.x.toFixed(2));
			$('#y').text(pos.y.toFixed(2));

			if (item) {
				if (previousPoint != item.dataIndex) {
					previousPoint = item.dataIndex;

					$('#gridtip').remove();
					var x = item.datapoint[0].toFixed(0),
						y = item.datapoint[1].toFixed(0);

					showTooltip(item.pageX, item.pageY,
								'x : ' + x + '&nbsp;&nbsp;&nbsp; y : ' + y);
				}
			}
			else {
				$('#gridtip').remove();
				previousPoint = null;
			}
		});
    }
});
