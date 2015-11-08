package vn.nitt.showapp;

import android.content.Intent;
import android.net.Uri;
import android.support.v7.app.ActionBarActivity;
import android.os.Bundle;
import android.util.Log;
import android.view.View;


public class ShowActivity extends ActionBarActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_show);
    }

    public void goBrowser(View v){
        //implicit intent
        Intent i = new Intent(Intent.ACTION_VIEW, Uri.parse("http://vnexpress.net"));
        startActivity(i);
    }

    public void goPhone(View v){
        //explicit
        Log.d("ok", "Chay vao day khong");
        startActivity(new Intent(Intent.ACTION_CALL, Uri.parse("tel:0912106550")));
    }

    public void sellFlower(View v){
        //explicit
        Intent i = new Intent(this, FlowerActivity.class);
        startActivity(i);
    }
}
