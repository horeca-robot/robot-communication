package com.horecarobot.robotcommunication.Messages;

public class Command<T> {
    private String type = "";
    private T payload = null;

    public Command() {

    }

    public Command(String type, T payload) {
        this.type = type;
        this.payload = payload;
    }

    public String getType() {
        return this.type;
    }

    public void setType(String type) {
        this.type = type;
    }

    public T getPayload() {
        return this.payload;
    }

    public void setPayload(T payload) {
        this.payload = payload;
    }

    @Override
    public String toString() {
        return "{" +
            " type='" + getType() + "'" +
            "}";
    }
}
