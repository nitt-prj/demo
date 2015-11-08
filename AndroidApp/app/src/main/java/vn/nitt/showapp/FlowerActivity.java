package vn.nitt.showapp;

import android.support.v7.app.ActionBarActivity;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;


public class FlowerActivity extends ActionBarActivity {

    private EditText edtFlower;
    private Button btnBuy;
    private TextView brought;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_flower);

        //ui
        edtFlower = (EditText) findViewById(R.id.edtFlower);
        btnBuy = (Button) findViewById(R.id.btnBuy);
        brought = (TextView) findViewById(R.id.brought);

        //add event
        btnBuy.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                buyFlower(v);
            }
        });
    }

    public void buyFlower(View v) {
        //lay ten bong hoa
        String flowerName = edtFlower.getText().toString();

        //nhet vao text view
        brought.append("\n"+flowerName);

        //reset edit text
        edtFlower.setText("");
    }
}
