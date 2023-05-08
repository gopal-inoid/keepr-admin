<?php 
              $tax_amt = json_decode($tax_data[0]->tax_amt);
            ?>
                                        <select name="product_id" class="form-control" disabled>
                                            @if(!empty($tax_amt))
                                                @foreach($tax_amt as $pro)
                                                    <option >{{$pro->state}}</option>
                                                @endforeach
                                            @endif
                                        </select>